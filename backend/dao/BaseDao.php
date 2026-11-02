<?php
require_once __DIR__ . '/../config/config.php';

class BaseDao
{
    protected PDO $connection;

    public function __construct(protected string $table)
    {
        $dsn = 'mysql:host=' . Config::DB_HOST()
             . ';dbname=' . Config::DB_NAME()
             . ';charset=utf8mb4';
        $this->connection = new PDO(
            $dsn,
            Config::DB_USER(),
            Config::DB_PASSWORD(),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    public function getAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function insert(array $data): array
    {
        $columns = implode(', ', array_keys($data));
        $params = ':' . implode(', :', array_keys($data));
        $stmt = $this->connection->prepare(
            "INSERT INTO {$this->table} ($columns) VALUES ($params)"
        );
        $stmt->execute($data);
        $data['id'] = (int) $this->connection->lastInsertId();
        return $data;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        foreach (array_keys($data) as $column) {
            $fields[] = "$column = :$column";
        }
        $stmt = $this->connection->prepare(
            "UPDATE {$this->table} SET " . implode(', ', $fields)
            . " WHERE id = :id"
        );
        $data['id'] = $id;
        $stmt->execute($data);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM {$this->table} WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
