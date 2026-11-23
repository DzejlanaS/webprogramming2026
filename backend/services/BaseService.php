<?php
require_once __DIR__ . '/../dao/BaseDao.php';

class BaseService
{
    public function __construct(protected BaseDao $dao) {}

    public function getAll(): array
    {
        return $this->dao->getAll();
    }

    public function getById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->dao->getById($id);
    }

    public function delete(int $id): bool
    {
        if ($this->getById($id) === null) {
            return false;
        }
        return $this->dao->delete($id);
    }

    protected function requiredText(array $data, string $field, string $label, int $min, int $max): string
    {
        $value = $this->toText($data[$field] ?? null, $label);
        if ($value === '') {
            throw new InvalidArgumentException("$label is required");
        }
        if (mb_strlen($value) < $min) {
            throw new InvalidArgumentException("$label must be at least $min characters");
        }
        if (mb_strlen($value) > $max) {
            throw new InvalidArgumentException("$label is too long (max $max)");
        }
        return $value;
    }

    protected function optionalText(array $data, string $field, string $label, int $max): ?string
    {
        $value = $this->toText($data[$field] ?? null, $label);
        if ($value === '') {
            return null;
        }
        if (mb_strlen($value) > $max) {
            throw new InvalidArgumentException("$label is too long (max $max)");
        }
        return $value;
    }

    protected function requiredEmail(array $data): string
    {
        $email = mb_strtolower($this->requiredText($data, 'email', 'Email', 3, 100));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Email is not valid');
        }
        return $email;
    }

    protected function toInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && preg_match('/^-?[0-9]+$/', trim($value))) {
            return (int) trim($value);
        }
        return null;
    }

    private function toText(mixed $value, string $label): string
    {
        if ($value === null) {
            return '';
        }
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            throw new InvalidArgumentException("$label must be text");
        }
        return trim((string) $value);
    }
}
