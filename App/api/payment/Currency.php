<?php
declare(strict_types=1);

class Currency
{
    /**
     * Supported currencies
     */
    public const SUPPORTED = [
        'AZN',
        'USD',
        'EUR',
        'TRY',
        'RUB',
        'GBP',
        'CHF',
        'JPY',
        'AED',
        'CNY'
    ];

    /**
     * Validate currency code
     */
    public static function isSupported(string $currency): bool
    {
        return in_array(strtoupper($currency), self::SUPPORTED, true);
    }

    /**
     * Returns all exchange rates.
     *
     * Format:
     * [
     *   AZN => 1,
     *   USD => 1.70,
     *   EUR => 1.99
     * ]
     */
    public static function getRates(): array
    {
        $url = "https://www.cbar.az/currencies/" . date('d.m.Y') . ".xml";

        $xml = @simplexml_load_file($url);

        if (!$xml) {
            throw new Exception("Currency rates unavailable.");
        }

        $rates = [
            'AZN' => 1.0
        ];

        foreach ($xml->ValType as $type) {

            foreach ($type->Valute as $valute) {

                $code = strtoupper((string)$valute['Code']);

                $nominal = (float)$valute->Nominal;
                $value = (float)$valute->Value;

                if ($nominal <= 0) {
                    continue;
                }

                $rates[$code] = $value / $nominal;
            }
        }

        return $rates;
    }

    /**
     * Convert currency
     */
    public static function convert(
        float $amount,
        string $from,
        string $to,
        ?array $rates = null
    ): float {

        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return round($amount, 2);
        }

        if ($rates === null) {
            $rates = self::getRates();
        }

        if (!isset($rates[$from])) {
            throw new Exception("Unsupported currency: {$from}");
        }

        if (!isset($rates[$to])) {
            throw new Exception("Unsupported currency: {$to}");
        }

        // FROM -> AZN
        $azn = $amount * $rates[$from];

        // AZN -> TARGET
        $converted = $azn / $rates[$to];

        return round($converted, 2);
    }
}