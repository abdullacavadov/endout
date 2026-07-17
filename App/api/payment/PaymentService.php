<?php
declare(strict_types=1);

class PaymentService
{
    private PDO $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }



    /**
     * Sifariş üçün payment yaradır
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO payments (
            order_id,
            cust_id,
            provider,
            provider_order_id,
            amount,
            currency,
            status,
            created_at
        )
        VALUES (
            :order_id,
            :cust_id,
            :provider,
            :provider_order_id,
            :amount,
            :currency,
            'initiated',
            NOW()
        )
    ");


        $stmt->execute([

            ':order_id' => $data['order_id'],

            ':cust_id' => $data['cust_id'],

            ':provider' => $data['provider'] ?? null,

            ':provider_order_id' => $data['provider_order_id'] ?? null,

            ':amount' => $data['amount'],

            ':currency' => $data['currency'] ?? 'AZN'

        ]);


        return (int) $this->pdo->lastInsertId();
    }





    /**
     * Payment status dəyişir
     */
    public function updateStatus(
        int $paymentId,
        string $status
    ): bool {

        $stmt = $this->pdo->prepare("
            UPDATE payments
            SET status = :status
            WHERE id = :id
        ");


        return $stmt->execute([

            ':status' => $status,

            ':id' => $paymentId

        ]);
    }






    /**
     * Gateway transaction qeyd edir
     */
    public function addTransaction(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO payment_transactions (
                payment_id,
                transaction_id,
                provider_reference,
                request_data,
                response_data,
                status,
                created_at
            )
            VALUES (
                :payment_id,
                :transaction_id,
                :provider_reference,
                :request_data,
                :response_data,
                :status,
                NOW()
            )
        ");


        $stmt->execute([

            ':payment_id' => $data['payment_id'],

            ':transaction_id' => $data['transaction_id'] ?? null,

            ':provider_reference' => $data['provider_reference'] ?? null,

            ':request_data' => isset($data['request_data'])
                ? json_encode($data['request_data'], JSON_UNESCAPED_UNICODE)
                : null,

            ':response_data' => isset($data['response_data'])
                ? json_encode($data['response_data'], JSON_UNESCAPED_UNICODE)
                : null,

            ':status' => $data['status'] ?? 'created'

        ]);


        return (int) $this->pdo->lastInsertId();
    }






    /**
     * Payment məlumatını order ilə birlikdə gətirir
     */
    public function findByOrder(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                p.*,
                o.total AS order_total
            FROM payments p

            INNER JOIN orders o
                ON o.id = p.order_id

            WHERE p.order_id = :order_id

            ORDER BY p.id DESC

            LIMIT 1
        ");


        $stmt->execute([
            ':order_id' => $orderId
        ]);


        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        return $result ?: null;
    }

}