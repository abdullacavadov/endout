<?php
declare(strict_types=1);

class MarketContext
{
    private PDO $pdo;

    /**
     * Cache
     */
    private array $contexts = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Market məlumatlarını qaytarır
     */
    public function get(int $marketId): array
    {
        if (isset($this->contexts[$marketId])) {
            return $this->contexts[$marketId];
        }

        /*
        |--------------------------------------------------------------------------
        | Market
        |--------------------------------------------------------------------------
        */

        $stmt = $this->pdo->prepare("
            SELECT
                m.id,
                m.customer_id,
                m.country_id,
                c.coefficient

            FROM markets m

            INNER JOIN countries c
                ON c.id = m.country_id

            WHERE m.id = ?

            LIMIT 1
        ");

        $stmt->execute([$marketId]);

        $market = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$market) {
            throw new Exception("Market not found.");
        }

        /*
        |--------------------------------------------------------------------------
        | Audience
        |--------------------------------------------------------------------------
        */

        $stmt = $this->pdo->prepare("
            SELECT
                COALESCE(SUM(c.country_audience),0)

            FROM market_ad_countries mac

            INNER JOIN countries c
                ON c.id = mac.country_id

            WHERE mac.market_id = ?
        ");

        $stmt->execute([$marketId]);

        $audience = (float)$stmt->fetchColumn();

        $context = [

            'market_id'     => (int)$market['id'],

            'customer_id'   => (int)$market['customer_id'],

            'country_id'    => (int)$market['country_id'],

            'coefficient'   => (float)$market['coefficient'],

            'audience'      => $audience

        ];

        $this->contexts[$marketId] = $context;

        return $context;
    }

    /**
     * Market mövcuddurmu?
     */
    public function exists(int $marketId): bool
    {
        return isset($this->contexts[$marketId]) || !empty($this->get($marketId));
    }

    /**
     * Market istifadəçiyə məxsusdursa context qaytarır
     */
    public function getForCustomer(int $marketId, int $customerId): array
    {
        $context = $this->get($marketId);

        if ($context['customer_id'] !== $customerId) {
            throw new Exception("Unauthorized market.");
        }

        return $context;
    }

    /**
     * Pulsuz paket üçün ölkə dəstəklənirmi?
     */
    public function hasAudienceCountry(int $countryId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT id

            FROM countries

            WHERE id = ?
              AND country_audience > 0

            LIMIT 1
        ");

        $stmt->execute([$countryId]);

        return (bool)$stmt->fetchColumn();
    }
}