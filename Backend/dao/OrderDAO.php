<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/BaseDAO.php';

class OrderDAO extends BaseDAO {
    protected function meta(): array {
        return [
            'orders',
            'order_id',
            ['user_id','order_date','status','total_amount']
        ];
    }

    /**
     * Recalculate order total from order_item lines.
     */
    public function recalcTotal(int $orderId): bool {
        $sql = "UPDATE `orders` o
                JOIN (
                  SELECT order_id, COALESCE(SUM(line_total),0) AS total
                  FROM order_item
                  WHERE order_id = :oid
                ) t ON t.order_id = o.order_id
                SET o.total_amount = t.total
                WHERE o.order_id = :oid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':oid' => $orderId]);
    }
}
