<?php

Flight::route('GET /contacts', function () {
    $userId = Flight::request()->query['user_id'] ?? null;
    if ($userId === null) {
        Flight::json(Flight::contactService()->getAll());
        return;
    }
    $contacts = Flight::contactService()->getByUserId($userId);
    if ($contacts === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json($contacts);
});

Flight::route('GET /contacts/@id:[0-9]+', function ($id) {
    $contact = Flight::contactService()->getById((int) $id);
    if ($contact === null) {
        Flight::json(['error' => 'Contact message not found'], 404);
        return;
    }
    Flight::json($contact);
});

Flight::route('POST /contacts', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::contactService()->create($data), 201);
});

Flight::route('PUT /contacts/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    $updated = Flight::contactService()->update((int) $id, $data);
    if ($updated === null) {
        Flight::json(['error' => 'Contact message not found'], 404);
        return;
    }
    Flight::json($updated);
});

Flight::route('DELETE /contacts/@id:[0-9]+', function ($id) {
    if (!Flight::contactService()->delete((int) $id)) {
        Flight::json(['error' => 'Contact message not found'], 404);
        return;
    }
    Flight::json(['message' => 'Contact message deleted']);
});
