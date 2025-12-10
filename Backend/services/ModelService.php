<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ModelDAO.php';

class ModelService extends BaseService {

    public function __construct(PDO $pdo) {
        $this->dao = new ModelDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['brand_id'])) {
            throw new InvalidArgumentException('brand_id is required.');
        }
        if (empty($data['name'])) {
            throw new InvalidArgumentException('Model name is required.');
        }

        $this->validateYears($data);
    }

    protected function validateUpdate(array &$data): void {
        $this->validateYears($data);
    }

    private function validateYears(array &$data): void {
        if (isset($data['year_from']) && isset($data['year_to']) &&
            $data['year_from'] !== null && $data['year_to'] !== null &&
            (int)$data['year_from'] > (int)$data['year_to']) {
            throw new InvalidArgumentException('year_from cannot be greater than year_to.');
        }
    }
}
