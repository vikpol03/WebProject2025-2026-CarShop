<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class ReviewDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'review',
            'review_id',
            ['user_id','car_id','rating','comment','created_at']
        ];
    }

    public function listForCar(int $carId, int $limit = 50, int $offset = 0): array {
        return $this->list($limit, $offset, ['car_id' => $carId]);
    }

    public function listForUser(int $userId, int $limit = 50, int $offset = 0): array {
        return $this->list($limit, $offset, ['user_id' => $userId]);
    }
}
