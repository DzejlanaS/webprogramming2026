<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/OrderItemDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class OrderService extends BaseService
{
    private const PAYMENT_METHOD = 'cash_on_delivery';
    private const STATUS_CHANGES = [
        'pending'   => ['shipped', 'cancelled'],
        'shipped'   => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function __construct(
        OrderDao $orderDao = new OrderDao(),
        private OrderItemDao $orderItemDao = new OrderItemDao(),
        private ProductDao $productDao = new ProductDao(),
        private UserDao $userDao = new UserDao(),
    ) {
        parent::__construct($orderDao);
    }

    public function getAll(): array
    {
        return $this->dao->getLatest();
    }

    public function getById(int $id): ?array
    {
        $order = parent::getById($id);
        if ($order === null) {
            return null;
        }
        $order['items'] = $this->orderItemDao->getByOrderId($id);
        return $order;
    }

    public function getByUserId(int $userId): ?array
    {
        if ($userId <= 0 || $this->userDao->getById($userId) === null) {
            return null;
        }
        return $this->dao->getByUserId($userId);
    }

    public function placeOrder(array $data): array
    {
        $userId = $this->toInt($data['user_id'] ?? null);
        if ($userId === null || $userId <= 0 || $this->userDao->getById($userId) === null) {
            throw new InvalidArgumentException('user_id is required and must belong to an existing user');
        }

        $delivery = $this->validateDelivery($data);
        $quantities = $this->collectQuantities($data['items'] ?? null);

        $items = [];
        $totalCents = 0;
        foreach ($quantities as $productId => $quantity) {
            $product = $this->productDao->getById($productId);
            if ($product === null) {
                throw new InvalidArgumentException("Product $productId does not exist");
            }
            if ($quantity > $product['stock_quantity']) {
                throw new DomainException("Not enough stock for {$product['name']} (only {$product['stock_quantity']} left)");
            }
            $priceCents = (int) round((float) $product['price'] * 100);
            $subtotalCents = $priceCents * $quantity;
            $totalCents += $subtotalCents;
            $items[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'unit_price' => $priceCents / 100,
                'subtotal'   => $subtotalCents / 100,
            ];
        }

        $order = array_merge($delivery, [
            'user_id'        => $userId,
            'order_number'   => $this->generateOrderNumber(),
            'total_amount'   => $totalCents / 100,
            'status'         => 'pending',
            'payment_method' => self::PAYMENT_METHOD,
        ]);

        $orderId = $this->dao->createOrder($order, $items);
        return $this->getById($orderId);
    }

    public function update(int $id, array $data): ?array
    {
        $existing = parent::getById($id);
        if ($existing === null) {
            return null;
        }
        if (self::STATUS_CHANGES[$existing['status']] === []) {
            throw new DomainException('A ' . $existing['status'] . ' order cannot be changed');
        }

        $merged = array_merge($existing, $data);
        $delivery = $this->validateDelivery($merged);

        $status = $merged['status'];
        if (!is_string($status) || !array_key_exists($status, self::STATUS_CHANGES)) {
            throw new InvalidArgumentException('Status must be pending, shipped, delivered or cancelled');
        }
        if ($status !== $existing['status'] && !in_array($status, self::STATUS_CHANGES[$existing['status']], true)) {
            throw new DomainException("Order status cannot change from {$existing['status']} to $status");
        }

        $this->dao->update($id, array_merge($delivery, ['status' => $status]));

        if ($status === 'cancelled') {
            $this->returnStock($id);
        }

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $order = parent::getById($id);
        if ($order === null) {
            return false;
        }
        if (!in_array($order['status'], ['delivered', 'cancelled'], true)) {
            throw new DomainException('Only delivered or cancelled orders can be deleted. Cancel the order first.');
        }
        return $this->dao->delete($id);
    }

    private function validateDelivery(array $data): array
    {
        $phone = $this->requiredText($data, 'phone', 'Phone', 6, 20);
        if (!preg_match('/^[0-9+()\s-]{6,20}$/', $phone)) {
            throw new InvalidArgumentException('Phone may only contain digits, spaces and + ( ) -');
        }

        $postalCode = $this->requiredText($data, 'postal_code', 'Postal code', 4, 6);
        if (!preg_match('/^[0-9]{4,6}$/', $postalCode)) {
            throw new InvalidArgumentException('Postal code must have 4 to 6 digits');
        }

        return [
            'full_name'   => $this->requiredText($data, 'full_name', 'Full name', 2, 100),
            'phone'       => $phone,
            'email'       => $this->requiredEmail($data),
            'street'      => $this->requiredText($data, 'street', 'Street', 3, 150),
            'city'        => $this->requiredText($data, 'city', 'City', 2, 50),
            'postal_code' => $postalCode,
            'notes'       => $this->optionalText($data, 'notes', 'Notes', 255),
        ];
    }

    private function collectQuantities(mixed $items): array
    {
        if (!is_array($items) || count($items) === 0) {
            throw new InvalidArgumentException('An order needs at least one item');
        }

        $quantities = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException('Each item needs a product_id and a quantity');
            }
            $productId = $this->toInt($item['product_id'] ?? null);
            if ($productId === null || $productId <= 0) {
                throw new InvalidArgumentException('Each item needs a valid product_id');
            }
            $quantity = $this->toInt($item['quantity'] ?? null);
            if ($quantity === null || $quantity < 1) {
                throw new InvalidArgumentException('Quantity must be a whole number of at least 1');
            }
            $quantities[$productId] = ($quantities[$productId] ?? 0) + $quantity;
        }
        return $quantities;
    }

    private function returnStock(int $orderId): void
    {
        foreach ($this->orderItemDao->getByOrderId($orderId) as $item) {
            $product = $this->productDao->getById($item['product_id']);
            $this->productDao->update($item['product_id'], [
                'stock_quantity' => $product['stock_quantity'] + $item['quantity'],
            ]);
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'PET-' . random_int(100000, 999999);
        } while ($this->dao->getByOrderNumber($number) !== null);
        return $number;
    }
}
