<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BrandDAO.php';

class BrandService extends BaseService {

    public function __construct(PDO $pdo) {
        $this->dao = new BrandDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['name'])) {
            throw new InvalidArgumentException('Brand name is required.');
        }
    }

    protected function validateUpdate(array &$data): void {
        if (isset($data['name']) && $data['name'] === '') {
            throw new InvalidArgumentException('Brand name cannot be empty.');
        }
    }
}
