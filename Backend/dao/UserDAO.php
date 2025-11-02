<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class UserDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'user',
            'user_id',
            ['email', 'password_hash', 'full_name', 'created_at']
        ];
    }

    // Helper to create with password hashing if "password" provided
    public function createUser(array $data): int {
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        return $this->create($data);
    }
}
