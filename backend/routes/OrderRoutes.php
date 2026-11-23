<?php

Flight::route('GET /orders', function () {
    Flight::json(Flight::orderService()->getAll());
});

Flight::route('GET /orders/@id:[0-9]+', function ($id) {
    $order = Flight::orderService()->getById((int) $id);
    if ($order === null) {
        Flight::json(['error' => 'Order not found'], 404);
        return;
    }
    Flight::json($order);
});

Flight::route('POST /orders', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::orderService()->placeOrder($data), 201);
});

Flight::route('PUT /orders/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    $updated = Flight::orderService()->update((int) $id, $data);
    if ($updated === null) {
        Flight::json(['error' => 'Order not found'], 404);
        return;
    }
    Flight::json($updated);
});

Flight::route('DELETE /orders/@id:[0-9]+', function ($id) {
    if (!Flight::orderService()->delete((int) $id)) {
        Flight::json(['error' => 'Order not found'], 404);
        return;
    }
    Flight::json(['message' => 'Order deleted']);
});
