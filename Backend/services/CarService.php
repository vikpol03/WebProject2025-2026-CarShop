<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CarDAO.php';

class CarService extends BaseService {

    public function __construct(PDO $pdo) {
        $this->dao = new CarDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['model_id'])) {
            throw new InvalidArgumentException('model_id is required.');
        }
        if (!isset($data['price']) || !is_numeric($data['price']) || (float)$data['price'] <= 0) {
            throw new InvalidArgumentException('price must be a positive number.');
        }

        if (isset($data['mileage_km']) && (int)$data['mileage_km'] < 0) {
            throw new InvalidArgumentException('mileage_km cannot be negative.');
        }

        // Normalize in_stock to 0/1
        if (isset($data['in_stock'])) {
            $data['in_stock'] = (int)!!$data['in_stock'];
        }
    }

    protected function validateUpdate(array &$data): void {
        if (isset($data['price']) && (!is_numeric($data['price']) || (float)$data['price'] <= 0)) {
            throw new InvalidArgumentException('price must be a positive number.');
        }

        if (isset($data['mileage_km']) && (int)$data['mileage_km'] < 0) {
            throw new InvalidArgumentException('mileage_km cannot be negative.');
        }

        if (isset($data['in_stock'])) {
            $data['in_stock'] = (int)!!$data['in_stock'];
        }
    }
}
