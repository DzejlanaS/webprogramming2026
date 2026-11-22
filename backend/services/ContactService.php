<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ContactDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class ContactService extends BaseService
{
    public function __construct(
        ContactDao $contactDao = new ContactDao(),
        private UserDao $userDao = new UserDao(),
    ) {
        parent::__construct($contactDao);
    }

    public function getAll(): array
    {
        return $this->dao->getLatest();
    }

    public function getByUserId(mixed $userId): ?array
    {
        $id = $this->toInt($userId);
        if ($id === null || $id <= 0) {
            throw new InvalidArgumentException('user_id must be a positive whole number');
        }
        if ($this->userDao->getById($id) === null) {
            return null;
        }
        return $this->dao->getByUserId($id);
    }

    public function create(array $data): array
    {
        $created = $this->dao->insert($this->validate($data));
        return $this->getById($created['id']);
    }

    public function update(int $id, array $data): ?array
    {
        $existing = $this->getById($id);
        if ($existing === null) {
            return null;
        }
        $this->dao->update($id, $this->validate(array_merge($existing, $data)));
        return $this->getById($id);
    }

    private function validate(array $data): array
    {
        $userId = $data['user_id'] ?? null;
        if ($userId !== null && $userId !== '') {
            $userId = $this->toInt($userId);
            if ($userId === null || $userId <= 0 || $this->userDao->getById($userId) === null) {
                throw new InvalidArgumentException('user_id must belong to an existing user or be left out');
            }
        } else {
            $userId = null;
        }

        return [
            'user_id' => $userId,
            'name'    => $this->requiredText($data, 'name', 'Name', 2, 100),
            'email'   => $this->requiredEmail($data),
            'subject' => $this->requiredText($data, 'subject', 'Subject', 3, 150),
            'message' => $this->requiredText($data, 'message', 'Message', 10, 2000),
        ];
    }
}
