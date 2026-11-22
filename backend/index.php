<?php
require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Europe/Sarajevo');

require_once __DIR__ . '/utils/Logger.php';
require_once __DIR__ . '/services/ExternalApiException.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/ProductService.php';
require_once __DIR__ . '/services/OrderService.php';
require_once __DIR__ . '/services/OrderItemService.php';
require_once __DIR__ . '/services/ContactService.php';
require_once __DIR__ . '/services/BreedFactService.php';

Flight::register('userService', 'UserService');
Flight::register('categoryService', 'CategoryService');
Flight::register('productService', 'ProductService');
Flight::register('orderService', 'OrderService');
Flight::register('orderItemService', 'OrderItemService');
Flight::register('contactService', 'ContactService');
Flight::register('breedFactService', 'BreedFactService');

require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/CategoryRoutes.php';
require_once __DIR__ . '/routes/ProductRoutes.php';
require_once __DIR__ . '/routes/OrderRoutes.php';
require_once __DIR__ . '/routes/OrderItemRoutes.php';
require_once __DIR__ . '/routes/ContactRoutes.php';
require_once __DIR__ . '/routes/BreedFactRoutes.php';

Flight::map('error', function (Throwable $e) {
    $status = match (true) {
        $e instanceof InvalidArgumentException => 400,
        $e instanceof DomainException => 409,
        $e instanceof ExternalApiException => 503,
        default => 500,
    };
    if ($status === 500) {
        $request = Flight::request();
        Logger::error(
            $request->method . ' ' . $request->url . ' 500 ' . get_class($e) . ': ' . $e->getMessage()
            . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'
        );
        Flight::jsonHalt(['error' => 'Something went wrong on the server. Please try again later.'], 500);
    }
    Flight::jsonHalt(['error' => $e->getMessage()], $status);
});

Flight::map('notFound', function () {
    Flight::json(['error' => 'Route not found'], 404);
});

Flight::map('methodNotFound', function () {
    Flight::json(['error' => 'Method not allowed'], 405);
});

Flight::start();
