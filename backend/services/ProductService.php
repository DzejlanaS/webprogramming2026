<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/OrderItemDao.php';

class ProductService extends BaseService
{
    private const PET_TYPES = ['dog', 'cat', 'both'];
    private const MAX_FEATURED = 4;
    private const MAX_PRICE = 99999.99;
    private const MAX_STOCK = 100000;

    public function __construct(
        ProductDao $productDao = new ProductDao(),
        private CategoryDao $categoryDao = new CategoryDao(),
        private OrderItemDao $orderItemDao = new OrderItemDao(),
    ) {
        parent::__construct($productDao);
    }

    public function getFiltered(array $filters): array
    {
        $categoryId = null;
        if (($filters['category_id'] ?? null) !== null) {
            $categoryId = $this->toInt($filters['category_id']);
            if ($categoryId === null || $categoryId <= 0) {
                throw new InvalidArgumentException('category_id must be a positive whole number');
            }
        }

        $search = $this->optionalText($filters, 'search', 'Search term', 100);

        $petType = $filters['pet_type'] ?? null;
        if ($petType !== null && !in_array($petType, ['dog', 'cat'], true)) {
            throw new InvalidArgumentException('pet_type must be dog or cat');
        }

        $featuredOnly = false;
        if (($filters['featured'] ?? null) !== null) {
            $featured = filter_var($filters['featured'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            if ($featured === null) {
                throw new InvalidArgumentException('featured must be true or false');
            }
            $featuredOnly = $featured;
        }

        return $this->dao->getFiltered($categoryId, $search, $petType, $featuredOnly);
    }

    public function getById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->dao->getByIdWithCategory($id);
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
        $this->dao->update($id, $this->validate(array_merge($existing, $data), $existing));
        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        if ($this->getById($id) === null) {
            return false;
        }
        if (count($this->orderItemDao->getByProductId($id)) > 0) {
            throw new DomainException('A product that has been ordered cannot be deleted');
        }
        return $this->dao->delete($id);
    }

    private function validate(array $data, ?array $existing): array
    {
        $categoryId = $this->toInt($data['category_id'] ?? null);
        if ($categoryId === null || $categoryId <= 0) {
            throw new InvalidArgumentException('category_id is required and must be a positive whole number');
        }
        if ($this->categoryDao->getById($categoryId) === null) {
            throw new InvalidArgumentException('Category does not exist');
        }

        $name = $this->requiredText($data, 'name', 'Name', 2, 100);
        $description = $this->optionalText($data, 'description', 'Description', 2000);

        $price = $data['price'] ?? null;
        $validPrice = is_int($price)
            || is_float($price) && round($price, 2) == $price
            || is_string($price) && preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', trim($price));
        if (!$validPrice) {
            throw new InvalidArgumentException('Price is required and must be a number with at most 2 decimals');
        }
        $price = round((float) $price, 2);
        if ($price <= 0 || $price > self::MAX_PRICE) {
            throw new InvalidArgumentException('Price must be greater than 0 and at most ' . self::MAX_PRICE);
        }

        $stock = $this->toInt($data['stock_quantity'] ?? 0);
        if ($stock === null || $stock < 0 || $stock > self::MAX_STOCK) {
            throw new InvalidArgumentException('Stock quantity must be a whole number from 0 to ' . self::MAX_STOCK);
        }

        $imageUrl = $this->optionalText($data, 'image_url', 'Image URL', 255);
        if ($imageUrl !== null && (filter_var($imageUrl, FILTER_VALIDATE_URL) === false || !preg_match('#^https?://#i', $imageUrl))) {
            throw new InvalidArgumentException('Image URL must be a valid http or https address');
        }

        $isFeatured = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if ($isFeatured === null) {
            throw new InvalidArgumentException('is_featured must be true or false');
        }
        $wasFeatured = $existing !== null && (int) $existing['is_featured'] === 1;
        if ($isFeatured && !$wasFeatured && count($this->dao->getFeatured()) >= self::MAX_FEATURED) {
            throw new DomainException('At most ' . self::MAX_FEATURED . ' products can be featured at the same time');
        }

        $petType = $data['pet_type'] ?? 'both';
        if (!in_array($petType, self::PET_TYPES, true)) {
            throw new InvalidArgumentException('pet_type must be dog, cat or both');
        }

        return [
            'category_id'    => $categoryId,
            'name'           => $name,
            'description'    => $description,
            'price'          => $price,
            'stock_quantity' => $stock,
            'image_url'      => $imageUrl,
            'is_featured'    => $isFeatured ? 1 : 0,
            'pet_type'       => $petType,
        ];
    }
}
