<?php
declare(strict_types=1);

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/Currency.php";
require_once __DIR__ . "/Calculator.php";
require_once __DIR__ . "/PackageRepository.php";
require_once __DIR__ . "/MarketContext.php";

require_once __DIR__ . "/../_csrf.php";
require_once __DIR__ . "/../_helpers.php";

header('Content-Type: application/json');

try {

    require_login($pdo);

    csrf_verify($_POST['_csrf'] ?? '');

    $packageId = (int) ($_POST['package_id'] ?? 0);
    $months = (int) ($_POST['duration'] ?? 1);
    $currency = strtoupper($_POST['currency'] ?? 'AZN');
    $marketId = (int) ($_POST['market_id'] ?? 0);

    if (!Currency::isSupported($currency)) {
        throw new Exception("Unsupported currency.");
    }

    /*
    |--------------------------------------------------------------------------
    | Market ownership
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            country_id
        FROM markets
        WHERE id = ?
        AND customer_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $marketId,
        $_SESSION['customer_id']
    ]);

    $market = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$market) {
        throw new Exception("Unauthorized market.");
    }

    /*
    |--------------------------------------------------------------------------
    | Calculator
    |--------------------------------------------------------------------------
    */



    $packageRepository = new PackageRepository($pdo);

    $marketContext = new MarketContext($pdo);

    $calculator = new Calculator(
        $packageRepository,
        $marketContext
    );



    /*
    |--------------------------------------------------------------------------
    | All packages
    |--------------------------------------------------------------------------
    */

    $packages = $calculator->calculateAllPackages(
        $marketId,
        $months,
        $currency
    );

    /*
    |--------------------------------------------------------------------------
    | Selected package
    |--------------------------------------------------------------------------
    */

    $selected = $calculator->calculate(
        $marketId,
        $packageId,
        $months,
        $currency
    );

    /*
    |--------------------------------------------------------------------------
    | Countries
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            country_audience
        FROM countries
        ORDER BY name
    ");

    $allCountries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([

        'ok' => true,

        'total' => $selected['display_amount'],

        'packages' => $packages,

        'features' => $selected['features'],

        'countries' => [],

        'allCountries' => $allCountries,

        'marketCountry' => (int) $market['country_id']

    ]);

} catch (Throwable $e) {

    //http_response_code(400);

    // echo json_encode([
    //     'ok' => false,
    //     'message' => $e->getMessage()
    // ]);


    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);

}