<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderItemDAO.php';

class OrderItemService extends BaseService {

    public function __construct(PDO $pdo) {
        $this->dao = new OrderItemDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['order_id']) || empty($data['car_id'])) {
            throw new InvalidArgumentException('order_id and car_id are required.');
        }
        $this->computeAndValidateTotals($data);
    }

    protected function validateUpdate(array &$data): void {
        $this->computeAndValidateTotals($data);
    }

    private function computeAndValidateTotals(array &$data): void {
        if (!isset($data['quantity']) || (int)$data['quantity'] <= 0) {
            throw new InvalidArgumentException('quantity must be > 0.');
        }
        if (!isset($data['unit_price']) || (float)$data['unit_price'] < 0) {
            throw new InvalidArgumentException('unit_price must be >= 0.');
        }

        $qty  = (int)$data['quantity'];
        $unit = (float)$data['unit_price'];
        $data['line_total'] = $qty * $unit;
    }
}
