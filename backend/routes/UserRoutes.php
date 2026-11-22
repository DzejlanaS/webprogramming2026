<?php

Flight::route('GET /users', function () {
    $role = Flight::request()->query['role'] ?? null;
    Flight::json(Flight::userService()->getAll($role));
});

Flight::route('GET /users/@id:[0-9]+', function ($id) {
    $user = Flight::userService()->getById((int) $id);
    if ($user === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json($user);
});

Flight::route('GET /users/@id:[0-9]+/orders', function ($id) {
    $orders = Flight::orderService()->getByUserId((int) $id);
    if ($orders === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json($orders);
});

Flight::route('POST /users', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userService()->create($data), 201);
});

Flight::route('PUT /users/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    $updated = Flight::userService()->update((int) $id, $data);
    if ($updated === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json($updated);
});

Flight::route('DELETE /users/@id:[0-9]+', function ($id) {
    if (!Flight::userService()->delete((int) $id)) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json(['message' => 'User deleted']);
});
