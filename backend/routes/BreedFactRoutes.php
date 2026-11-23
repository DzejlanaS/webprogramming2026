<?php

Flight::route('GET /products/@id:[0-9]+/breed-facts', function ($id) {
    $facts = Flight::breedFactService()->getForProduct((int) $id);
    if ($facts === null) {
        Flight::json(['error' => 'Product not found'], 404);
        return;
    }
    Flight::json($facts);
});
