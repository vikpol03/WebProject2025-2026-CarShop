<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ImageDAO.php';

class ImageService extends BaseService {

    public function __construct(PDO $pdo) {
        $this->dao = new ImageDAO($pdo);
    }

    protected function validateCreate(array &$data): void {
        if (empty($data['car_id'])) {
            throw new InvalidArgumentException('car_id is required.');
        }
        if (empty($data['url'])) {
            throw new InvalidArgumentException('Image url is required.');
        }
    }

    protected function validateUpdate(array &$data): void {
        if (isset($data['url']) && $data['url'] === '') {
            throw new InvalidArgumentException('Image url cannot be empty.');
        }
    }
}
