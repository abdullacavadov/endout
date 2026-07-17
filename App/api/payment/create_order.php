<?php
declare(strict_types=1);


require_once __DIR__ . "/../../inc/config.php";

require_once __DIR__ . "/PackageRepository.php";
require_once __DIR__ . "/MarketContext.php";
require_once __DIR__ . "/Calculator.php";
require_once __DIR__ . "/OrderService.php";

require_once __DIR__ . "/../_csrf.php";
require_once __DIR__ . "/../_helpers.php";


header('Content-Type: application/json');


try {


    require_login($pdo);


    $customerId = (int) $_SESSION['customer_id'];
    //$customerId = 40; // Test üçün müvəqqəti olaraq istifadə olunur


    $input = json_decode(
        file_get_contents("php://input"),
        true
    );


    if (!is_array($input)) {
        throw new Exception("Invalid request data");
    }



    /*
    |--------------------------------------------------------------------------
    | Input
    |--------------------------------------------------------------------------
    */


    $marketId = (int) ($input['market_id'] ?? 0);
    //$marketId = 36; // Test üçün müvəqqəti olaraq istifadə olunur
    
    $months = (int) ($input['months'] ?? 0);
    //$months = 12; // Test üçün müvəqqəti olaraq istifadə olunur

    $packageId = (int) ($input['package_id'] ?? 0);
    //$packageId = 11; // Test üçün müvəqqəti olaraq istifadə olunur



    if (!$marketId || !$months || !$packageId) {
        throw new Exception("Market, months və package tələb olunur");
    }




    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */


    $packageRepository = new PackageRepository($pdo);

    $marketContext = new MarketContext($pdo);


    $calculator = new Calculator(
        $packageRepository,
        $marketContext
    );


    $orderService = new OrderService($pdo);





    /*
    |--------------------------------------------------------------------------
    | Calculate
    |--------------------------------------------------------------------------
    */


    $packages = $calculator->calculateAllPackages(
        $marketId,
        $months,
        'AZN'
    );



    if (!isset($packages[$packageId])) {
        throw new Exception("Package calculation tapılmadı");
    }



    $packageResult = $packages[$packageId];



    if ($packageResult['invalid']) {
        throw new Exception("Bu paket üçün hesablama mümkün deyil");
    }





    /*
    |--------------------------------------------------------------------------
    | Package info
    |--------------------------------------------------------------------------
    */


    $packageInfo = $packageRepository->get($packageId);


    if (!$packageInfo) {
        throw new Exception("Package tapılmadı");
    }





    /*
    |--------------------------------------------------------------------------
    | Create Order
    |--------------------------------------------------------------------------
    */


    $orderId = $orderService->create([

        'customer_id' => $customerId,

        'currency' => $packageResult['payment_currency'],

        'subtotal' => $packageResult['payment_amount'],

        'discount' => 0,

        'total' => $packageResult['payment_amount'],


        'items' => [

            [

                'package_id' => $packageId,

                'package_name' => $packageInfo['name'],

                'qty' => 1,

                'price' => $packageResult['payment_amount'],

                'total' => $packageResult['payment_amount']

            ]

        ]

    ]);





    echo json_encode([

        'ok' => true,

        'order_id' => $orderId,

        'amount' => $packageResult['payment_amount'],

        'currency' => $packageResult['payment_currency']

    ]);



} catch (Throwable $e) {


    http_response_code(400);


    echo json_encode([

        'ok' => false,

        'message' => $e->getMessage(),

        'file' => $e->getFile(),

        'line' => $e->getLine()

    ]);

}