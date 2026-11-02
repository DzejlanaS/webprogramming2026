<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('users');
    }

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM users WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function getByRole(string $role): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM users WHERE role = :role ORDER BY last_name, first_name'
        );
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll();
    }
}
