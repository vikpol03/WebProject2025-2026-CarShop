<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

// Single PDO instance shared via Flight
$pdo = require __DIR__ . '/dao/database.php';
Flight::set('pdo', $pdo);

// Views folder for HTML templates
Flight::set('flight.views', __DIR__ . '/views');

// Map JSON response helper
Flight::map('json', function ($data, int $code = 200) {
    $resp = Flight::response();
    $resp->status($code);
    $resp->header('Content-Type', 'application/json; charset=utf-8');
    $resp->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    $resp->send();
});

// Load services & routes
require __DIR__ . '/services/UserService.php';
require __DIR__ . '/services/BrandService.php';
require __DIR__ . '/services/ModelService.php';
require __DIR__ . '/services/CarService.php';
require __DIR__ . '/services/OrderService.php';
require __DIR__ . '/services/OrderItemService.php';
require __DIR__ . '/services/ImageService.php';
require __DIR__ . '/services/ReviewService.php';

require __DIR__ . '/routes';

Flight::start();
