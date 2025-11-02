<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class OrderItemDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'order_item',
            'order_item_id',
            ['order_id','car_id','quantity','unit_price','line_total']
        ];
    }
}
