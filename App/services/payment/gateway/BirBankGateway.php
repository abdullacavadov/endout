<?php
declare(strict_types=1);

require_once __DIR__ . '/PaymentGatewayInterface.php';

class BirBankGateway implements PaymentGatewayInterface
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $callbackUrl;

    public function __construct(
        string $baseUrl,
        string $username,
        string $password,
        string $callbackUrl
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->username = $username;
        $this->password = $password;
        $this->callbackUrl = $callbackUrl;
    }


    private function mapStatus(string $status): string
    {
        return match (strtolower(trim($status))) {

            'preparing',
            'being prepared' => 'initiated',

            'authorized',
            'fully paid',
            'partially paid',
            'funded',
            'closed' => 'paid',

            'refunded' => 'refunded',

            'cancelled',
            'rejected',
            'refused',
            'expired',
            'declined',
            'voided' => 'failed',

            default => 'failed',
        };
    }

    public function refundPayment(array $payment): array
    {
        throw new Exception('Not implemented.');
    }

    public function createPayment(array $payment): array
    {


        $response = $this->request(
            'POST',
            '/order',
            [
                'order' => [
                    'typeRid' => 'Order_SMS',
                    'amount' => number_format((float) $payment['amount'], 2, '.', ''),
                    'currency' => $payment['currency'],
                    'language' => 'az',
                    'title' => 'Endout',
                    'description' => 'Order #' . $payment['order_id'],
                    'hppRedirectUrl' => $this->callbackUrl,
                ]
            ]
        );

        // var_dump($response);
        // exit;

        if (!isset($response['order'])) {
            throw new Exception('Invalid BirBank response.');
        }

        $order = $response['order'];

        return [
            'success' => true,
            'provider_payment_id' => (string) $order['id'],
            'status' => $this->mapStatus($order['status']),
            'raw_response' => $response,
            'redirect_url' => rtrim($order['hppUrl'], '/')
                . '?id='
                . $order['id']
                . '&password='
                . urlencode($order['password']),
        ];
    }

    public function verifyPayment(array $payload): array
    {
        if (empty($payload['ID'])) {
            throw new Exception('Provider payment ID is missing.');
        }

        $providerPaymentId = (string) $payload['ID'];

        $response = $this->request(
            'GET',
            '/order/' . urlencode($providerPaymentId)
        );

        if (!isset($response['order'])) {
            throw new Exception('Invalid BirBank response.');
        }

        $order = $response['order'];

        return [
            'success' => true,
            'provider_payment_id' => (string) $order['id'],
            'provider_reference' => $order['id'],
            'status' => $this->mapStatus($order['status']),
            'raw_response' => $response
        ];
    }

    /**
     * Generic HTTP request
     */
    private function request(
        string $method,
        string $endpoint,
        ?array $body = null
    ): array {

        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->username . ':' . $this->password,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception(curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new Exception('Invalid JSON response from BirBank.');
        }


        if ($httpCode >= 400) {
            throw new Exception(
                $data['errorDescription']
                ?? $data['message']
                ?? 'BirBank request failed.'
            );
        }


        if (isset($data['errorCode'])) {
            throw new Exception(
                $data['errorDescription']
                ?? $data['errorCode']
            );
        }


        return $data;
    }
}