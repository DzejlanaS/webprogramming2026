<?php

Flight::route('GET /categories', function () {
    Flight::json(Flight::categoryService()->getAll());
});

Flight::route('GET /categories/@id:[0-9]+', function ($id) {
    $category = Flight::categoryService()->getById((int) $id);
    if ($category === null) {
        Flight::json(['error' => 'Category not found'], 404);
        return;
    }
    Flight::json($category);
});

Flight::route('GET /categories/@id:[0-9]+/products', function ($id) {
    $products = Flight::categoryService()->getProducts((int) $id);
    if ($products === null) {
        Flight::json(['error' => 'Category not found'], 404);
        return;
    }
    Flight::json($products);
});

Flight::route('POST /categories', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::categoryService()->create($data), 201);
});

Flight::route('PUT /categories/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    $updated = Flight::categoryService()->update((int) $id, $data);
    if ($updated === null) {
        Flight::json(['error' => 'Category not found'], 404);
        return;
    }
    Flight::json($updated);
});

Flight::route('DELETE /categories/@id:[0-9]+', function ($id) {
    if (!Flight::categoryService()->delete((int) $id)) {
        Flight::json(['error' => 'Category not found'], 404);
        return;
    }
    Flight::json(['message' => 'Category deleted']);
});
