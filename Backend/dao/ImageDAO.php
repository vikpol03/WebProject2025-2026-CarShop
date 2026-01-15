<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class ImageDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'image',
            'image_id',
            ['car_id','url','sort_order']
        ];
    }

    public function listForCar(int $carId, int $limit = 50, int $offset = 0): array {
        return $this->list($limit, $offset, ['car_id' => $carId]);
    }
}
