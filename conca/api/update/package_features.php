<?php
require_once("../../inc/config.php");

try {

    if (empty($_POST['features']) || !is_array($_POST['features'])) {
        exit('Məlumat göndərilməyib.');
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO package_features
        (
            package_id,
            feature_key,
            feature_value,
            is_active
        )
        VALUES
        (
            :package_id,
            :feature_key,
            :feature_value,
            1
        )
        ON DUPLICATE KEY UPDATE

            feature_value = VALUES(feature_value),
            is_active = 1,
            updated_at = NOW()
    ");

    foreach ($_POST['features'] as $packageId => $features) {

        if (!is_numeric($packageId)) {
            continue;
        }

        foreach ($features as $featureKey => $value) {

            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            $stmt->execute([

                ':package_id'    => (int)$packageId,
                ':feature_key'   => trim($featureKey),
                ':feature_value' => trim((string)$value)

            ]);
        }
    }

    $pdo->commit();

    exit('success');

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    exit($e->getMessage());

}