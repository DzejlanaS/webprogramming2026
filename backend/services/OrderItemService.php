<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderItemDao.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';

class OrderItemService extends BaseService
{
    public function __construct(
        OrderItemDao $orderItemDao = new OrderItemDao(),
        private OrderDao $orderDao = new OrderDao(),
        private ProductDao $productDao = new ProductDao(),
    ) {
        parent::__construct($orderItemDao);
    }

    public function getAll(): array
    {
        return $this->dao->getAllWithProduct();
    }

    public function getByOrderId(mixed $orderId): ?array
    {
        $orderId = $this->toInt($orderId);
        if ($orderId === null || $orderId <= 0) {
            throw new InvalidArgumentException('order_id must be a positive whole number');
        }
        if ($this->orderDao->getById($orderId) === null) {
            return null;
        }
        return $this->dao->getByOrderId($orderId);
    }

    public function getById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->dao->getByIdWithProduct($id);
    }

    public function create(array $data): array
    {
        $orderId = $this->toInt($data['order_id'] ?? null);
        if ($orderId === null || $orderId <= 0 || $this->orderDao->getById($orderId) === null) {
            throw new InvalidArgumentException('order_id is required and must belong to an existing order');
        }
        $this->requirePendingOrder($orderId);

        $productId = $this->toInt($data['product_id'] ?? null);
        $product = $productId !== null && $productId > 0 ? $this->productDao->getById($productId) : null;
        if ($product === null) {
            throw new InvalidArgumentException('product_id is required and must belong to an existing product');
        }
        foreach ($this->dao->getByOrderId($orderId) as $item) {
            if ($item['product_id'] === $productId) {
                throw new DomainException('This product is already in the order. Change the quantity of that item instead.');
            }
        }

        $quantity = $this->validateQuantity($data);
        if ($quantity > $product['stock_quantity']) {
            throw new DomainException("Not enough stock for {$product['name']} (only {$product['stock_quantity']} left)");
        }

        $priceCents = (int) round((float) $product['price'] * 100);
        $created = $this->dao->insert([
            'order_id'   => $orderId,
            'product_id' => $productId,
            'quantity'   => $quantity,
            'unit_price' => $priceCents / 100,
            'subtotal'   => $priceCents * $quantity / 100,
        ]);

        $this->changeStock($productId, -$quantity);
        $this->recalculateTotal($orderId);
        return $this->getById($created['id']);
    }

    public function update(int $id, array $data): ?array
    {
        $item = $this->dao->getById($id);
        if ($item === null) {
            return null;
        }
        $this->requirePendingOrder($item['order_id']);

        $quantity = $this->validateQuantity(array_merge($item, $data));
        $difference = $quantity - $item['quantity'];
        $product = $this->productDao->getById($item['product_id']);
        if ($difference > $product['stock_quantity']) {
            throw new DomainException("Not enough stock for {$product['name']} (only {$product['stock_quantity']} more available)");
        }

        $priceCents = (int) round((float) $item['unit_price'] * 100);
        $this->dao->update($id, [
            'quantity' => $quantity,
            'subtotal' => $priceCents * $quantity / 100,
        ]);

        $this->changeStock($item['product_id'], -$difference);
        $this->recalculateTotal($item['order_id']);
        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $item = $this->dao->getById($id);
        if ($item === null) {
            return false;
        }
        $this->requirePendingOrder($item['order_id']);
        if (count($this->dao->getByOrderId($item['order_id'])) <= 1) {
            throw new DomainException('An order needs at least one item. Cancel the order instead.');
        }

        $this->dao->delete($id);
        $this->changeStock($item['product_id'], $item['quantity']);
        $this->recalculateTotal($item['order_id']);
        return true;
    }

    private function requirePendingOrder(int $orderId): void
    {
        $order = $this->orderDao->getById($orderId);
        if ($order['status'] !== 'pending') {
            throw new DomainException('Items can only be changed while the order is pending');
        }
    }

    private function validateQuantity(array $data): int
    {
        $quantity = $this->toInt($data['quantity'] ?? null);
        if ($quantity === null || $quantity < 1) {
            throw new InvalidArgumentException('Quantity is required and must be a whole number of at least 1');
        }
        return $quantity;
    }

    private function changeStock(int $productId, int $change): void
    {
        if ($change === 0) {
            return;
        }
        $product = $this->productDao->getById($productId);
        $this->productDao->update($productId, [
            'stock_quantity' => $product['stock_quantity'] + $change,
        ]);
    }

    private function recalculateTotal(int $orderId): void
    {
        $totalCents = 0;
        foreach ($this->dao->getByOrderId($orderId) as $item) {
            $totalCents += (int) round((float) $item['subtotal'] * 100);
        }
        $this->orderDao->update($orderId, ['total_amount' => $totalCents / 100]);
    }
}
