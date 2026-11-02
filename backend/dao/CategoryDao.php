<?php
require_once __DIR__ . '/BaseDao.php';

class CategoryDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('categories');
    }

    public function getByName(string $name): ?array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM categories WHERE name = :name'
        );
        $stmt->execute(['name' => $name]);
        return $stmt->fetch() ?: null;
    }

    public function getAllWithProductCount(): array
    {
        $stmt = $this->connection->query(
            'SELECT c.id, c.name, c.description, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id, c.name, c.description
             ORDER BY c.id'
        );
        return $stmt->fetchAll();
    }
}
