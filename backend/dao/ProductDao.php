<?php
require_once __DIR__ . '/BaseDao.php';

class ProductDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('products');
    }

    public function getAllWithCategory(): array
    {
        $stmt = $this->connection->query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             INNER JOIN categories c ON p.category_id = c.id
             ORDER BY p.id'
        );
        return $stmt->fetchAll();
    }

    public function getByCategory(int $categoryId): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM products WHERE category_id = :category_id ORDER BY name'
        );
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll();
    }

    public function getFeatured(): array
    {
        $stmt = $this->connection->query(
            'SELECT * FROM products WHERE is_featured = 1 ORDER BY id'
        );
        return $stmt->fetchAll();
    }

    public function search(string $term): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM products WHERE name LIKE :term ORDER BY name'
        );
        $stmt->execute(['term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }
}
