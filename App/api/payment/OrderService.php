<?php
declare(strict_types=1);

class OrderService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Yeni sifariş yaradır
     */
    public function create(array $data): int
    {
        $this->pdo->beginTransaction();

        try {

            $stmt = $this->pdo->prepare("
                INSERT INTO orders (
                    customer_id,
                    currency,
                    subtotal,
                    discount,
                    total,
                    status,
                    created_at
                )
                VALUES (
                    :customer_id,
                    :currency,
                    :subtotal,
                    :discount,
                    :total,
                    :status,
                    NOW()
                )
            ");

            $stmt->execute([
                ':customer_id' => $data['customer_id'],
                ':currency'  => $data['currency'] ?? 'AZN',
                ':subtotal'  => $data['subtotal'],
                ':discount'  => $data['discount'] ?? 0,
                ':total'     => $data['total'],
                ':status'    => 'pending'
            ]);


            $orderId = (int)$this->pdo->lastInsertId();


            if (!empty($data['items'])) {

                $this->addItems(
                    $orderId,
                    $data['items']
                );

            }


            $this->pdo->commit();

            return $orderId;


        } catch (Throwable $e) {

            $this->pdo->rollBack();
            throw $e;

        }
    }



    /**
     * Sifariş paketlərini əlavə edir
     */
    private function addItems(int $orderId, array $items): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_items (
                order_id,
                package_id,
                package_name,
                qty,
                price,
                total
            )
            VALUES (
                :order_id,
                :package_id,
                :package_name,
                :qty,
                :price,
                :total
            )
        ");


        foreach ($items as $item) {

            $stmt->execute([
                ':order_id'     => $orderId,
                ':package_id'   => $item['package_id'],
                ':package_name' => $item['package_name'],
                ':qty'          => $item['qty'],
                ':price'        => $item['price'],
                ':total'        => $item['total']
            ]);

        }
    }



    /**
     * Status dəyişmək üçün
     */
    public function updateStatus(
        int $orderId,
        string $status
    ): bool {

        $stmt = $this->pdo->prepare("
            UPDATE orders
            SET status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $orderId
        ]);
    }


}