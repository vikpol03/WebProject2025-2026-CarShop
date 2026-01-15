<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class CarDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'car',
            'car_id',
            ['model_id','vin','trim','color','transmission','mileage_km','price','in_stock','listed_at']
        ];
    }

    public function listByModel(int $modelId, int $limit = 50, int $offset = 0): array {
        return $this->list($limit, $offset, ['model_id' => $modelId]);
    }

    public function findByVIN(string $vin): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM `car` WHERE `vin` = :vin");
        $stmt->execute([':vin' => $vin]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
