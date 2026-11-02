<?php
require_once __DIR__ . '/BaseDao.php';

class ContactDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('contacts');
    }

    public function getLatest(): array
    {
        $stmt = $this->connection->query(
            'SELECT * FROM contacts ORDER BY created_at DESC, id DESC'
        );
        return $stmt->fetchAll();
    }

    public function getByUserId(int $userId): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM contacts WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
