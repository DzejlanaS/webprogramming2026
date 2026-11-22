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

    public function getByIdWithCategory(int $id): ?array
    {
        $stmt = $this->connection->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             INNER JOIN categories c ON p.category_id = c.id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getFiltered(?int $categoryId, ?string $search, ?string $petType, bool $featuredOnly): array
    {
        $sql = 'SELECT p.*, c.name AS category_name
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE 1 = 1';
        $params = [];
        if ($categoryId !== null) {
            $sql .= ' AND p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        if ($search !== null) {
            $sql .= ' AND p.name LIKE :term';
            $params['term'] = '%' . $search . '%';
        }
        if ($petType !== null) {
            $sql .= " AND p.pet_type IN (:pet_type, 'both')";
            $params['pet_type'] = $petType;
        }
        if ($featuredOnly) {
            $sql .= ' AND p.is_featured = 1';
        }
        $stmt = $this->connection->prepare($sql . ' ORDER BY p.id');
        $stmt->execute($params);
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
