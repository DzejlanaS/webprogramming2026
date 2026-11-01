<?php
require_once __DIR__ . '/BaseDao.php';

class OrderDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('orders');
    }

    public function getByUserId(int $userId): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getByOrderNumber(string $orderNumber): ?array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM orders WHERE order_number = :order_number'
        );
        $stmt->execute(['order_number' => $orderNumber]);
        return $stmt->fetch() ?: null;
    }

    public function createOrder(array $order, array $items): int
    {
        try {
            $this->connection->beginTransaction();

            $columns = implode(', ', array_keys($order));
            $params = ':' . implode(', :', array_keys($order));
            $stmt = $this->connection->prepare(
                "INSERT INTO orders ($columns) VALUES ($params)"
            );
            $stmt->execute($order);
            $orderId = (int) $this->connection->lastInsertId();

            $stmt = $this->connection->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                 VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)'
            );
            foreach ($items as $item) {
                $stmt->execute([
                    'order_id'   => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }

            $this->connection->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
