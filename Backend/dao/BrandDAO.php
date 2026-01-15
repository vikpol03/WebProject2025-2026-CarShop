<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class BrandDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'brand',
            'brand_id',
            ['name', 'country']
        ];
    }
}
