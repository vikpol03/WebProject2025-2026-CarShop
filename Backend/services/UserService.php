<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDAO.php';

class UserService extends BaseService {

    public function __construct(PDO $pdo) {
        $this->dao = new UserDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Valid email is required.');
        }

        if (empty($data['full_name'])) {
            throw new InvalidArgumentException('Full name is required.');
        }

        if (empty($data['password']) || strlen((string)$data['password']) < 6) {
            throw new InvalidArgumentException('Password must be at least 6 characters.');
        }

        // Hashing is done in DAO->createUser, but we still enforce presence here.
    }

    protected function validateUpdate(array &$data): void {
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email.');
        }

        if (isset($data['password'])) {
            if (strlen((string)$data['password']) < 6) {
                throw new InvalidArgumentException('Password must be at least 6 characters.');
            }
        }
    }

    // Override create to call the special DAO method that hashes password
    public function create(array $data): int {
        $this->validateCreate($data);
        /** @var UserDAO $dao */
        $dao = $this->dao;
        return $dao->createUser($data);
    }
}
