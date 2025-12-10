<?php
declare(strict_types=1);

function get_service(string $resource): ?BaseService {
    /** @var PDO $pdo */
    $pdo = Flight::get('pdo');

    return match ($resource) {
        'users'       => new UserService($pdo),
        'brands'      => new BrandService($pdo),
        'models'      => new ModelService($pdo),
        'cars'        => new CarService($pdo),
        'orders'      => new OrderService($pdo),
        'order-items' => new OrderItemService($pdo),
        'images'      => new ImageService($pdo),
        'reviews'     => new ReviewService($pdo),
        default       => null,
    };
}

/**
 * REST API routes: /api/{resource} and /api/{resource}/{id}
 */

// LIST + SINGLE
Flight::route('GET /api/@resource(/@id:[0-9]+)', function (string $resource, ?int $id = null) {
    $service = get_service($resource);
    if (!$service) {
        return Flight::json(['error' => 'Unknown resource'], 404);
    }

    if ($id !== null) {
        $row = $service->get($id);
        return $row
            ? Flight::json($row)
            : Flight::json(['error' => 'Not found'], 404);
    }

    $limit  = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 50;
    $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
    $filters = $_GET;
    unset($filters['limit'], $filters['offset']);

    return Flight::json([
        'items'  => $service->list($limit, $offset, $filters),
        'limit'  => $limit,
        'offset' => $offset,
    ]);
});

// CREATE
Flight::route('POST /api/@resource', function (string $resource) {
    $service = get_service($resource);
    if (!$service) {
        return Flight::json(['error' => 'Unknown resource'], 404);
    }

    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true) ?: [];

    try {
        if ($resource === 'users' && $service instanceof UserService) {
            $id = $service->create($data); // userService::create already hashes
        } elseif ($resource === 'orders' && $service instanceof OrderService) {
            $id = $service->create($data);
            if (!empty($data['recalc'])) {
                $service->recalcTotal($id);
            }
        } else {
            $id = $service->create($data);
        }

        return Flight::json(['id' => $id], 201);
    } catch (Throwable $e) {
        return Flight::json(['error' => $e->getMessage()], 400);
    }
});

// UPDATE
Flight::route('PUT|PATCH /api/@resource/@id:[0-9]+', function (string $resource, int $id) {
    $service = get_service($resource);
    if (!$service) {
        return Flight::json(['error' => 'Unknown resource'], 404);
    }

    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true) ?: [];

    try {
        $ok = $service->update($id, $data);

        if ($resource === 'orders' && $service instanceof OrderService && !empty($data['recalc'])) {
            $service->recalcTotal($id);
        }

        return Flight::json(['updated' => (bool)$ok]);
    } catch (Throwable $e) {
        return Flight::json(['error' => $e->getMessage()], 400);
    }
});

// DELETE
Flight::route('DELETE /api/@resource/@id:[0-9]+', function (string $resource, int $id) {
    $service = get_service($resource);
    if (!$service) {
        return Flight::json(['error' => 'Unknown resource'], 404);
    }

    try {
        $ok = $service->delete($id);
        return Flight::json(['deleted' => (bool)$ok]);
    } catch (Throwable $e) {
        return Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * HTML presentation (dynamic page with Flight::render)
 */

// Home page: list latest cars
Flight::route('GET /', function () {
    /** @var PDO $pdo */
    $pdo = Flight::get('pdo');
    $carService = new CarService($pdo);

    $cars = $carService->list(10, 0, ['in_stock' => 1]);

    Flight::render('home.php', [
        'cars' => $cars
    ]);
});

/**
 * Swagger/OpenAPI docs routes
 */

// Serve OpenAPI JSON
Flight::route('GET /openapi.json', function () {
    $spec = file_get_contents(__DIR__ . '/openapi.json');
    $resp = Flight::response();
    $resp->header('Content-Type', 'application/json; charset=utf-8');
    $resp->write($spec);
    $resp->send();
});

// Swagger UI page
Flight::route('GET /docs', function () {
    Flight::render('swagger.php');
});
