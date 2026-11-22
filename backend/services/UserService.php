<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/OrderDao.php';

class UserService extends BaseService
{
    private const ROLES = ['customer', 'admin'];

    public function __construct(
        UserDao $userDao = new UserDao(),
        private OrderDao $orderDao = new OrderDao(),
    ) {
        parent::__construct($userDao);
    }

    public function getAll(mixed $role = null): array
    {
        if ($role !== null) {
            if (!in_array($role, self::ROLES, true)) {
                throw new InvalidArgumentException('Role must be customer or admin');
            }
            $users = $this->dao->getByRole($role);
        } else {
            $users = $this->dao->getAll();
        }
        return array_map(fn (array $user) => $this->withoutPassword($user), $users);
    }

    public function getById(int $id): ?array
    {
        $user = parent::getById($id);
        return $user === null ? null : $this->withoutPassword($user);
    }

    public function create(array $data): array
    {
        $user = $this->validate($data, null);
        $created = $this->dao->insert($user);
        return $this->getById($created['id']);
    }

    public function update(int $id, array $data): ?array
    {
        $existing = $this->getById($id);
        if ($existing === null) {
            return null;
        }
        $user = $this->validate(array_merge($existing, $data), $id);
        $this->dao->update($id, $user);
        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        if ($this->getById($id) === null) {
            return false;
        }
        if (count($this->orderDao->getByUserId($id)) > 0) {
            throw new DomainException('A user who has placed orders cannot be deleted');
        }
        return $this->dao->delete($id);
    }

    private function validate(array $data, ?int $id): array
    {
        $firstName = $this->requiredText($data, 'first_name', 'First name', 2, 50);
        $lastName = $this->requiredText($data, 'last_name', 'Last name', 2, 50);
        foreach (['First name' => $firstName, 'Last name' => $lastName] as $label => $name) {
            if (!preg_match("/^\p{L}[\p{L} '-]*$/u", $name)) {
                throw new InvalidArgumentException("$label may only contain letters, spaces, hyphens and apostrophes");
            }
        }

        $email = $this->requiredEmail($data);
        $owner = $this->dao->getByEmail($email);
        if ($owner !== null && $owner['id'] !== $id) {
            throw new DomainException('A user with this email already exists');
        }

        $role = $data['role'] ?? 'customer';
        if (!in_array($role, self::ROLES, true)) {
            throw new InvalidArgumentException('Role must be customer or admin');
        }

        $user = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'role'       => $role,
        ];

        if ($id === null || array_key_exists('password', $data)) {
            $user['password_hash'] = password_hash($this->validatePassword($data['password'] ?? null), PASSWORD_DEFAULT);
        }

        return $user;
    }

    private function validatePassword(mixed $password): string
    {
        if (!is_string($password) || $password === '') {
            throw new InvalidArgumentException('Password is required');
        }
        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters');
        }
        if (strlen($password) > 72) {
            throw new InvalidArgumentException('Password is too long (max 72)');
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one letter and one number');
        }
        return $password;
    }

    private function withoutPassword(array $user): array
    {
        unset($user['password_hash']);
        return $user;
    }
}
