<?php
declare(strict_types=1);

class PackageRepository
{
    private PDO $pdo;

    /**
     * Cache
     */
    private array $packages = [];
    private array $features = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->loadPackages();
        $this->loadFeatures();
    }

    /**
     * Aktiv paketləri yaddaşa yüklə
     */
    private function loadPackages(): void
    {
        $stmt = $this->pdo->query("
            SELECT
                id,
                name,
                slug,
                duration_days,
                base_price,
                currency,
                description
            FROM packages
            WHERE is_active = 1
            ORDER BY sort_order, id
        ");

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $id = (int) $row['id'];

            $this->packages[$id] = [
                'id' => $id,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'duration_days' => (int) $row['duration_days'],
                'base_price' => (float) $row['base_price'],
                'currency' => strtoupper($row['currency']),
                'description' => $row['description']
            ];
        }
    }

    /**
     * Paket xüsusiyyətlərini yaddaşa yüklə
     */
    private function loadFeatures(): void
    {
        $stmt = $this->pdo->query("
            SELECT
                pf.package_id,
                pf.feature_key,
                pf.feature_value,
                fd.value_type,
                fd.sort_order

            FROM package_features pf

            LEFT JOIN feature_definitions fd
                ON fd.feature_key = pf.feature_key

            WHERE pf.is_active = 1

            ORDER BY
                pf.package_id,
                fd.sort_order
        ");

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $pid = (int) $row['package_id'];

            $value = $row['feature_value'];

            switch ($row['value_type']) {

                case 'bool':
                    $value = ((int) $value === 1);
                    break;

                case 'int':
                    $value = (int) $value;
                    break;

                case 'decimal':
                    $value = (float) $value;
                    break;

                case 'json':
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $value = $decoded;
                    }
                    break;
            }

            $this->features[$pid][] = [
                'feature_key' => $row['feature_key'],
                'feature_value' => $value
            ];
        }
    }

    /**
     * Bir paket
     */
    public function get(int $packageId): ?array
    {
        return $this->packages[$packageId] ?? null;
    }

    /**
     * Bütün paketlər
     */
    public function all(): array
    {
        return $this->packages;
    }

    /**
     * Paket feature-ları
     */
    public function getFeatures(int $packageId): array
    {
        return $this->features[$packageId] ?? [];
    }

    /**
     * Paket + feature birlikdə
     */
    public function getWithFeatures(int $packageId): ?array
    {
        if (!isset($this->packages[$packageId])) {
            return null;
        }

        $package = $this->packages[$packageId];
        $package['features'] = $this->getFeatures($packageId);

        return $package;
    }

    /**
     * Bütün paketlər + feature-lar
     */
    public function allWithFeatures(): array
    {
        $result = [];

        foreach ($this->packages as $id => $package) {

            $package['features'] = $this->getFeatures($id);

            $result[$id] = $package;
        }

        return $result;
    }

    /**
     * Aktiv paket ID-ləri
     */
    public function ids(): array
    {
        return array_keys($this->packages);
    }

    /**
     * Paket varmı?
     */
    public function exists(int $packageId): bool
    {
        return isset($this->packages[$packageId]);
    }
}