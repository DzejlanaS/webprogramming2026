<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';

class CategoryService extends BaseService
{
    public function __construct(
        CategoryDao $categoryDao = new CategoryDao(),
        private ProductDao $productDao = new ProductDao(),
    ) {
        parent::__construct($categoryDao);
    }

    public function getAll(): array
    {
        return $this->dao->getAllWithProductCount();
    }

    public function getProducts(int $id): ?array
    {
        if ($this->getById($id) === null) {
            return null;
        }
        return $this->productDao->getFiltered($id, null, null, false);
    }

    public function create(array $data): array
    {
        $created = $this->dao->insert($this->validate($data, null));
        return $this->getById($created['id']);
    }

    public function update(int $id, array $data): ?array
    {
        $existing = $this->getById($id);
        if ($existing === null) {
            return null;
        }
        $this->dao->update($id, $this->validate(array_merge($existing, $data), $id));
        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        if ($this->getById($id) === null) {
            return false;
        }
        if (count($this->productDao->getByCategory($id)) > 0) {
            throw new DomainException('A category that still has products cannot be deleted');
        }
        return $this->dao->delete($id);
    }

    private function validate(array $data, ?int $id): array
    {
        $name = $this->requiredText($data, 'name', 'Name', 2, 50);
        $existing = $this->dao->getByName($name);
        if ($existing !== null && $existing['id'] !== $id) {
            throw new DomainException('A category with this name already exists');
        }

        return [
            'name'        => $name,
            'description' => $this->optionalText($data, 'description', 'Description', 255),
        ];
    }
}
