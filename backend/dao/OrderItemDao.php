<?php
require_once __DIR__ . '/BaseDao.php';

class OrderItemDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('order_items');
    }

    public function getAllWithProduct(): array
    {
        $stmt = $this->connection->query(
            'SELECT oi.*, p.name AS product_name, p.image_url
             FROM order_items oi
             INNER JOIN products p ON oi.product_id = p.id
             ORDER BY oi.order_id, oi.id'
        );
        return $stmt->fetchAll();
    }

    public function getByIdWithProduct(int $id): ?array
    {
        $stmt = $this->connection->prepare(
            'SELECT oi.*, p.name AS product_name, p.image_url
             FROM order_items oi
             INNER JOIN products p ON oi.product_id = p.id
             WHERE oi.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getByOrderId(int $orderId): array
    {
        $stmt = $this->connection->prepare(
            'SELECT oi.*, p.name AS product_name, p.image_url
             FROM order_items oi
             INNER JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :order_id
             ORDER BY oi.id'
        );
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function getByProductId(int $productId): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM order_items WHERE product_id = :product_id ORDER BY id'
        );
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }
}
