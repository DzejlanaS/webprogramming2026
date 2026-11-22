<?php

Flight::route('GET /order-items', function () {
    $orderId = Flight::request()->query['order_id'] ?? null;
    if ($orderId === null) {
        Flight::json(Flight::orderItemService()->getAll());
        return;
    }
    $items = Flight::orderItemService()->getByOrderId($orderId);
    if ($items === null) {
        Flight::json(['error' => 'Order not found'], 404);
        return;
    }
    Flight::json($items);
});

Flight::route('GET /order-items/@id:[0-9]+', function ($id) {
    $item = Flight::orderItemService()->getById((int) $id);
    if ($item === null) {
        Flight::json(['error' => 'Order item not found'], 404);
        return;
    }
    Flight::json($item);
});

Flight::route('POST /order-items', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::orderItemService()->create($data), 201);
});

Flight::route('PUT /order-items/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    $updated = Flight::orderItemService()->update((int) $id, $data);
    if ($updated === null) {
        Flight::json(['error' => 'Order item not found'], 404);
        return;
    }
    Flight::json($updated);
});

Flight::route('DELETE /order-items/@id:[0-9]+', function ($id) {
    if (!Flight::orderItemService()->delete((int) $id)) {
        Flight::json(['error' => 'Order item not found'], 404);
        return;
    }
    Flight::json(['message' => 'Order item deleted']);
});
