<?php
// public/index.php
declare(strict_types=1);

header('Content-Type: application/json');

$pdo = require __DIR__ . '/../dao/database.php';

// autoload DAOs quickly
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/BrandDAO.php';
require_once __DIR__ . '/../dao/ModelDAO.php';
require_once __DIR__ . '/../dao/CarDAO.php';
require_once __DIR__ . '/../dao/OrderDAO.php';
require_once __DIR__ . '/../dao/OrderItemDAO.php';
require_once __DIR__ . '/../dao/ImageDAO.php';
require_once __DIR__ . '/../dao/ReviewDAO.php';

function body(): array {
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
function res($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// route pattern: /resource or /resource/{id}
$parts = array_values(array_filter(explode('/', $path)));
$resource = $parts[0] ?? '';
$id = isset($parts[1]) ? (int)$parts[1] : null;

$map = [
    'users'       => fn() => new UserDAO($GLOBALS['pdo']),
    'brands'      => fn() => new BrandDAO($GLOBALS['pdo']),
    'models'      => fn() => new ModelDAO($GLOBALS['pdo']),
    'cars'        => fn() => new CarDAO($GLOBALS['pdo']),
    'orders'      => fn() => new OrderDAO($GLOBALS['pdo']),
    'order_items' => fn() => new OrderItemDAO($GLOBALS['pdo']),
    'images'      => fn() => new ImageDAO($GLOBALS['pdo']),
    'reviews'     => fn() => new ReviewDAO($GLOBALS['pdo']),
];

if (!isset($map[$resource])) {
    res(['error' => 'Not found'], 404);
}
$dao = $map[$resource]();

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $row = $dao->findById($id);
                $row ? res($row) : res(['error' => 'Not found'], 404);
            } else {
                $limit  = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 50;
                $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

                // allow simple equality filters via ?col=value
                $filters = $_GET;
                unset($filters['limit'], $filters['offset']);

                res([
                    'items' => $dao->list($limit, $offset, $filters),
                    'limit' => $limit,
                    'offset'=> $offset
                ]);
            }
            break;

        case 'POST':
            $data = body();

            if ($resource === 'users') {
                /** @var UserDAO $dao */
                $newId = $dao->createUser($data);
            } else {
                $newId = $dao->create($data);
            }

            if ($resource === 'orders' && $newId && isset($data['recalc']) && $data['recalc']) {
                /** @var OrderDAO $dao */
                $dao->recalcTotal($newId);
            }

            res(['id' => $newId], 201);
            break;

        case 'PUT':
        case 'PATCH':
            if (!$id) res(['error' => 'Missing ID'], 400);
            $data = body();
            $ok = $dao->update($id, $data);

            if ($resource === 'orders' && $ok && ($data['recalc'] ?? false)) {
                /** @var OrderDAO $dao */
                $dao->recalcTotal($id);
            }

            res(['updated' => (bool)$ok]);
            break;

        case 'DELETE':
            if (!$id) res(['error' => 'Missing ID'], 400);
            $ok = $dao->delete($id);
            res(['deleted' => (bool)$ok]);
            break;

        default:
            res(['error' => 'Method not allowed'], 405);
    }
} catch (Throwable $e) {
    res(['error' => $e->getMessage()], 400);
}
