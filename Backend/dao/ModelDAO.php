<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class ModelDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'model',
            'model_id',
            ['brand_id', 'name', 'year_from', 'year_to']
        ];
    }
}
