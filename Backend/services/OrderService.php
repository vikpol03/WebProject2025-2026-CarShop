<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDAO.php';

class OrderService extends BaseService {

    private const ALLOWED_STATUS = ['pending', 'paid', 'cancelled'];

    public function __construct(PDO $pdo) {
        $this->dao = new OrderDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['user_id'])) {
            throw new InvalidArgumentException('user_id is required.');
        }

        if (isset($data['status']) && !in_array($data['status'], self::ALLOWED_STATUS, true)) {
            throw new InvalidArgumentException('Invalid order status.');
        }
    }

    protected function validateUpdate(array &$data): void {
        if (isset($data['status']) && !in_array($data['status'], self::ALLOWED_STATUS, true)) {
            throw new InvalidArgumentException('Invalid order status.');
        }
    }

    /** Convenience method used by controllers when they want to recalc total. */
    public function recalcTotal(int $orderId): bool {
        /** @var OrderDAO $dao */
        $dao = $this->dao;
        return $dao->recalcTotal($orderId);
    }
}
