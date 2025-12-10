<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReviewDAO.php';

class ReviewService extends BaseService {

    public function __construct(PDO $pdo) {
        $this->dao = new ReviewDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['user_id']) || empty($data['car_id'])) {
            throw new InvalidArgumentException('user_id and car_id are required.');
        }
        $this->validateRating($data);
    }

    protected function validateUpdate(array &$data): void {
        $this->validateRating($data);
    }

    private function validateRating(array &$data): void {
        if (isset($data['rating'])) {
            $r = (int)$data['rating'];
            if ($r < 1 || $r > 5) {
                throw new InvalidArgumentException('rating must be between 1 and 5.');
            }
        }
    }
}
