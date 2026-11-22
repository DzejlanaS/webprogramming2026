<?php

Flight::route('GET /products', function () {
    $query = Flight::request()->query;
    Flight::json(Flight::productService()->getFiltered([
        'category_id' => $query['category_id'] ?? null,
        'search'      => $query['search'] ?? null,
        'pet_type'    => $query['pet_type'] ?? null,
        'featured'    => $query['featured'] ?? null,
    ]));
});

Flight::route('GET /products/@id:[0-9]+', function ($id) {
    $product = Flight::productService()->getById((int) $id);
    if ($product === null) {
        Flight::json(['error' => 'Product not found'], 404);
        return;
    }
    Flight::json($product);
});

Flight::route('POST /products', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::productService()->create($data), 201);
});

Flight::route('PUT /products/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    $updated = Flight::productService()->update((int) $id, $data);
    if ($updated === null) {
        Flight::json(['error' => 'Product not found'], 404);
        return;
    }
    Flight::json($updated);
});

Flight::route('DELETE /products/@id:[0-9]+', function ($id) {
    if (!Flight::productService()->delete((int) $id)) {
        Flight::json(['error' => 'Product not found'], 404);
        return;
    }
    Flight::json(['message' => 'Product deleted']);
});
