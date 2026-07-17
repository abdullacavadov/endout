<?php
declare(strict_types=1);

require_once __DIR__ . "/Currency.php";
require_once __DIR__ . "/PackageRepository.php";
require_once __DIR__ . "/MarketContext.php";


class Calculator
{

    private PackageRepository $packages;

    private MarketContext $marketContext;

    private array $rates = [];


    public function __construct(
        PackageRepository $packages,
        MarketContext $marketContext
    ) {

        $this->packages = $packages;

        $this->marketContext = $marketContext;

        $this->rates = Currency::getRates();
    }



    /**
     * Tək paket hesablanması
     */
    public function calculate(
        int $marketId,
        int $packageId,
        int $months,
        string $displayCurrency = 'AZN'
    ): array {


        /*
        |--------------------------------------------------------------------------
        | Package
        |--------------------------------------------------------------------------
        */

        $package = $this->packages->get($packageId);


        if (!$package) {
            throw new Exception("Package not found.");
        }



        /*
        |--------------------------------------------------------------------------
        | Market Context
        |--------------------------------------------------------------------------
        */

        $context = $this->marketContext->get($marketId);



        if ($context['audience'] <= 0) {

            throw new Exception("Audience not found.");

        }



        /*
        |--------------------------------------------------------------------------
        | Free package validation
        |--------------------------------------------------------------------------
        */

        if ((float)$package['base_price'] <= 0) {


            if (!$this->marketContext->hasAudienceCountry(
                $context['country_id']
            )) {


                return [

                    'invalid' => true,

                    'display_amount' => null,

                    'payment_amount' => null,

                    'features' => $this->packages->getFeatures($packageId)

                ];

            }

        }




        /*
        |--------------------------------------------------------------------------
        | Base price -> USD
        |--------------------------------------------------------------------------
        */

        $priceUSD = Currency::convert(

            (float)$package['base_price'],

            $package['currency'],

            'USD',

            $this->rates

        );



        /*
        |--------------------------------------------------------------------------
        | Country coefficient
        |--------------------------------------------------------------------------
        */

        $priceUSD *= $context['coefficient'];



        /*
        |--------------------------------------------------------------------------
        | Audience
        |--------------------------------------------------------------------------
        */

        $priceUSD *= (
            $context['audience'] / 10000000
        );



        /*
        |--------------------------------------------------------------------------
        | Duration
        |--------------------------------------------------------------------------
        */

        $priceUSD *= $months;



        /*
        |--------------------------------------------------------------------------
        | Discount
        |--------------------------------------------------------------------------
        */

        $discount = $this->getDiscount($months);


        if ($discount > 0) {

            $priceUSD *= (1 - $discount);

        }




        /*
        |--------------------------------------------------------------------------
        | Display currency
        |--------------------------------------------------------------------------
        */

        $displayAmount = Currency::convert(

            $priceUSD,

            'USD',

            strtoupper($displayCurrency),

            $this->rates

        );




        /*
        |--------------------------------------------------------------------------
        | Bank payment amount (AZN)
        |--------------------------------------------------------------------------
        */

        $paymentAmount = Currency::convert(

            $priceUSD,

            'USD',

            'AZN',

            $this->rates

        );





        return [

            'invalid' => false,


            'package_id' => $packageId,


            'months' => $months,


            'display_amount' => round($displayAmount,2),

            'display_currency' => strtoupper($displayCurrency),



            'payment_amount' => round($paymentAmount,2),

            'payment_currency' => 'AZN',



            'features' => $this->packages->getFeatures($packageId)

        ];

    }





    /**
     * Bütün paketlərin hesablanması
     */
    public function calculateAllPackages(
        int $marketId,
        int $months,
        string $displayCurrency = 'AZN'
    ): array {


        $result = [];


        foreach ($this->packages->ids() as $packageId) {


            $calc = $this->calculate(

                $marketId,

                $packageId,

                $months,

                $displayCurrency

            );


            $result[$packageId] = [

                'invalid' => $calc['invalid'],

                'total' => $calc['display_amount'],

                'display_amount' => $calc['display_amount'],

                'display_currency' => $calc['display_currency'],

                'payment_amount' => $calc['payment_amount'],

                'payment_currency' => $calc['payment_currency'],

                'features' => $calc['features']

            ];

        }


        return $result;

    }





    /**
     * Müddət endirimi
     */
    private function getDiscount(int $months): float
    {

        return match($months) {

            3 => 0.05,

            6 => 0.10,

            12 => 0.15,

            default => 0

        };

    }

}