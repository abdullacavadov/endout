<?php
declare(strict_types=1);

class PaymentService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO payments (
                order_id, cust_id, provider, provider_order_id,
                amount, currency, status, created_at
            )
            VALUES (
                :order_id, :cust_id, :provider, :provider_order_id,
                :amount, :currency, 'initiated', NOW()
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

    public function updateGatewayData(
        int $paymentId,
        string $providerPaymentId,
        array $rawResponse
    ): bool {
        $stmt = $this->pdo->prepare("
            UPDATE payments
            SET provider_payment_id = :provider_payment_id,
                raw_response = :raw_response
            WHERE id = :id
        ");

        return $stmt->execute([
            ':provider_payment_id' => $providerPaymentId,
            ':raw_response' => json_encode($rawResponse, JSON_UNESCAPED_UNICODE),
            ':id' => $paymentId
        ]);
    }

    public function findByProviderPaymentId(
        string $providerPaymentId,
        bool $forUpdate = false
    ): ?array {
        $sql = "
            SELECT *
            FROM payments
            WHERE provider_payment_id = :provider_payment_id
            LIMIT 1
        ";

        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':provider_payment_id' => $providerPaymentId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function updateStatus(
        int $paymentId,
        string $status,
        bool $setPaidAt = false
    ): bool {
        $sql = "
            UPDATE payments
            SET status = :status
        ";

        if ($setPaidAt) {
            $sql .= ", paid_at = NOW()";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $paymentId
        ]);
    }

    public function attachSubscription(
        int $paymentId,
        int $subscriptionId
    ): bool {
        $stmt = $this->pdo->prepare("
            UPDATE payments
            SET sub_id = :sub_id
            WHERE id = :id
        ");

        return $stmt->execute([
            ':sub_id' => $subscriptionId,
            ':id' => $paymentId
        ]);
    }

    public function addTransaction(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO payment_transactions (
                payment_id, transaction_id, provider_reference,
                request_data, response_data, status, created_at
            )
            VALUES (
                :payment_id, :transaction_id, :provider_reference,
                :request_data, :response_data, :status, NOW()
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

    public function getOrderContext(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                o.id AS order_id,
                o.customer_id,
                o.total,
                o.currency,
                o.status AS order_status,
                oi.id AS order_item_id,
                oi.package_id,
                oi.package_name,
                oi.qty,
                oi.price,
                oi.total AS item_total,
                oi.months,
                p.duration_days,
                p.is_active AS package_is_active
            FROM orders o
            INNER JOIN order_items oi ON oi.order_id = o.id
            INNER JOIN packages p ON p.id = oi.package_id
            WHERE o.id = :order_id
            ORDER BY oi.id ASC
            LIMIT 1
        ");

        $stmt->execute([':order_id' => $orderId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function lockCustomer(int $customerId): void
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM customers
            WHERE id = :id
            FOR UPDATE
        ");

        $stmt->execute([':id' => $customerId]);

        if (!$stmt->fetchColumn()) {
            throw new Exception('Customer not found.');
        }
    }

    public function findLatestActiveSubscription(int $customerId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM customer_subs
            WHERE cust_id = :cust_id
              AND status = 'active'
            ORDER BY ends_at DESC, id DESC
            LIMIT 1
        ");

        $stmt->execute([':cust_id' => $customerId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function createSubscription(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO customer_subs (
                cust_id, package_id, status, starts_at, ends_at,
                auto_renew, cancel_at_period_end, price_paid,
                currency, created_at
            )
            VALUES (
                :cust_id, :package_id, 'active', :starts_at, :ends_at,
                0, 0, :price_paid, :currency, NOW()
            )
        ");

        $stmt->execute([
            ':cust_id' => $data['cust_id'],
            ':package_id' => $data['package_id'],
            ':starts_at' => $data['starts_at'],
            ':ends_at' => $data['ends_at'],
            ':price_paid' => $data['price_paid'],
            ':currency' => $data['currency']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function extendSubscription(
        int $subscriptionId,
        string $endsAt,
        float $pricePaid,
        string $currency
    ): bool {
        $stmt = $this->pdo->prepare("
            UPDATE customer_subs
            SET ends_at = :ends_at,
                price_paid = :price_paid,
                currency = :currency,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':ends_at' => $endsAt,
            ':price_paid' => $pricePaid,
            ':currency' => $currency,
            ':id' => $subscriptionId
        ]);
    }

    public function cancelSubscription(int $subscriptionId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE customer_subs
            SET
                status = 'canceled',
                canceled_at = NOW(),
                cancel_at_period_end = 0,
                updated_at = NOW()
            WHERE id = :id
              AND status = 'active'
        ");

        return $stmt->execute([
            ':id' => $subscriptionId
        ]);
    }

    public function findByOrder(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, o.total AS order_total
            FROM payments p
            INNER JOIN orders o ON o.id = p.order_id
            WHERE p.order_id = :order_id
            ORDER BY p.id DESC
            LIMIT 1
        ");

        $stmt->execute([':order_id' => $orderId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }
}
