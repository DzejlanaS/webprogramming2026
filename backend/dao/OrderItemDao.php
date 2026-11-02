<?php
require_once __DIR__ . '/BaseDao.php';

class OrderItemDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('order_items');
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
}
