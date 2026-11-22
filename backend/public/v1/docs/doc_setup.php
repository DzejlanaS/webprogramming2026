<?php
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Petly API',
    description: 'REST API of Petly, a pet shop for dogs and cats with delivery in Bosnia and Herzegovina - IT 2001 Web Programming.',
    version: '1.0'
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]
#[OA\Tag(name: 'users', description: 'Customers and shop admins')]
#[OA\Tag(name: 'categories', description: 'Product categories')]
#[OA\Tag(name: 'products', description: 'Products of the shop')]
#[OA\Tag(name: 'orders', description: 'Orders, paid cash on delivery')]
#[OA\Tag(name: 'order-items', description: 'Items inside an order')]
#[OA\Tag(name: 'contacts', description: 'Messages sent through the Contact Us page')]
#[OA\Tag(name: 'breed-facts', description: 'Dog and cat breed facts from TheDogAPI and TheCatAPI')]
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'error', type: 'string', example: 'Name is required'),
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Product deleted'),
    ]
)]
#[OA\Schema(
    schema: 'UserInput',
    required: ['first_name', 'last_name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', maxLength: 50, minLength: 2, example: 'Nermin'),
        new OA\Property(property: 'last_name', type: 'string', maxLength: 50, minLength: 2, example: 'Delić'),
        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 100, example: 'nermin.delic@example.com'),
        new OA\Property(
            property: 'password',
            type: 'string',
            maxLength: 72,
            minLength: 8,
            example: 'Secret123',
            description: 'At least 8 characters with a letter and a number. Required when creating, optional when updating.'
        ),
        new OA\Property(property: 'role', type: 'string', enum: ['customer', 'admin'], example: 'customer', description: 'Default customer'),
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'first_name', type: 'string', example: 'Amina'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Karić'),
        new OA\Property(property: 'email', type: 'string', example: 'amina.karic@example.com'),
        new OA\Property(property: 'role', type: 'string', enum: ['customer', 'admin'], example: 'customer'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-30 12:00:00'),
    ]
)]
#[OA\Schema(
    schema: 'CategoryInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 50, minLength: 2, example: 'Beds'),
        new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Beds and blankets for dogs and cats'),
    ]
)]
#[OA\Schema(
    schema: 'Category',
    allOf: [new OA\Schema(ref: '#/components/schemas/CategoryInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'product_count', type: 'integer', example: 2, description: 'Only in the list GET /categories'),
    ]
)]
#[OA\Schema(
    schema: 'ProductInput',
    required: ['category_id', 'name', 'price'],
    properties: [
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 2, example: 'Puppy Food 2kg'),
        new OA\Property(property: 'description', type: 'string', maxLength: 2000, nullable: true, example: 'Lamb and rice recipe for puppies.'),
        new OA\Property(property: 'price', type: 'number', example: 21.5, description: 'Greater than 0, at most 2 decimals'),
        new OA\Property(property: 'stock_quantity', type: 'integer', minimum: 0, example: 20, description: 'Default 0'),
        new OA\Property(property: 'image_url', type: 'string', maxLength: 255, nullable: true, example: 'https://images.unsplash.com/photo-1714068691210-073dc52c6c1d'),
        new OA\Property(property: 'is_featured', type: 'boolean', example: false, description: 'At most 4 products can be featured. Default false'),
        new OA\Property(property: 'pet_type', type: 'string', enum: ['dog', 'cat', 'both'], example: 'dog', description: 'Default both'),
    ]
)]
#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'category_name', type: 'string', example: 'Food'),
        new OA\Property(property: 'name', type: 'string', example: 'Premium Dog Food 3kg'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Grain-free, chicken & rice recipe for adult dogs.'),
        new OA\Property(property: 'price', type: 'string', example: '24.90', description: 'Decimal value in KM'),
        new OA\Property(property: 'stock_quantity', type: 'integer', example: 24),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'https://images.unsplash.com/photo-1714068691210-073dc52c6c1d'),
        new OA\Property(property: 'is_featured', type: 'integer', enum: [0, 1], example: 1),
        new OA\Property(property: 'pet_type', type: 'string', enum: ['dog', 'cat', 'both'], example: 'dog'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-30 12:00:00'),
    ]
)]
#[OA\Schema(
    schema: 'OrderLineInput',
    required: ['product_id', 'quantity'],
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 2),
    ]
)]
#[OA\Schema(
    schema: 'OrderInput',
    required: ['user_id', 'full_name', 'phone', 'email', 'street', 'city', 'postal_code', 'items'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 2),
        new OA\Property(property: 'full_name', type: 'string', maxLength: 100, minLength: 2, example: 'Amina Karić'),
        new OA\Property(property: 'phone', type: 'string', example: '+387 61 234 567', description: '6 to 20 digits, spaces and + ( ) -'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'amina.karic@example.com'),
        new OA\Property(property: 'street', type: 'string', maxLength: 150, minLength: 3, example: 'Zmaja od Bosne 12'),
        new OA\Property(property: 'city', type: 'string', maxLength: 50, minLength: 2, example: 'Sarajevo'),
        new OA\Property(property: 'postal_code', type: 'string', example: '71000', description: '4 to 6 digits'),
        new OA\Property(property: 'notes', type: 'string', maxLength: 255, nullable: true, example: 'Please call before delivery'),
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderLineInput')),
    ]
)]
#[OA\Schema(
    schema: 'OrderUpdateInput',
    properties: [
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'shipped', 'delivered', 'cancelled'], example: 'shipped'),
        new OA\Property(property: 'full_name', type: 'string', maxLength: 100, minLength: 2, example: 'Amina Karić'),
        new OA\Property(property: 'phone', type: 'string', example: '+387 61 234 567'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'amina.karic@example.com'),
        new OA\Property(property: 'street', type: 'string', maxLength: 150, minLength: 3, example: 'Zmaja od Bosne 12'),
        new OA\Property(property: 'city', type: 'string', maxLength: 50, minLength: 2, example: 'Sarajevo'),
        new OA\Property(property: 'postal_code', type: 'string', example: '71000'),
        new OA\Property(property: 'notes', type: 'string', maxLength: 255, nullable: true, example: 'Please call before delivery'),
    ]
)]
#[OA\Schema(
    schema: 'Order',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 2),
        new OA\Property(property: 'order_number', type: 'string', example: 'PET-104822'),
        new OA\Property(property: 'order_date', type: 'string', example: '2026-10-12 10:24:00'),
        new OA\Property(property: 'total_amount', type: 'string', example: '32.40', description: 'Decimal value in KM'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'shipped', 'delivered', 'cancelled'], example: 'delivered'),
        new OA\Property(property: 'payment_method', type: 'string', example: 'cash_on_delivery'),
        new OA\Property(property: 'full_name', type: 'string', example: 'Amina Karić'),
        new OA\Property(property: 'phone', type: 'string', example: '+387 61 234 567'),
        new OA\Property(property: 'email', type: 'string', example: 'amina.karic@example.com'),
        new OA\Property(property: 'street', type: 'string', example: 'Zmaja od Bosne 12'),
        new OA\Property(property: 'city', type: 'string', example: 'Sarajevo'),
        new OA\Property(property: 'postal_code', type: 'string', example: '71000'),
        new OA\Property(property: 'notes', type: 'string', nullable: true, example: null),
    ]
)]
#[OA\Schema(
    schema: 'OrderWithItems',
    allOf: [new OA\Schema(ref: '#/components/schemas/Order')],
    properties: [
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderItem')),
    ]
)]
#[OA\Schema(
    schema: 'OrderItemInput',
    required: ['order_id', 'product_id', 'quantity'],
    properties: [
        new OA\Property(property: 'order_id', type: 'integer', example: 2),
        new OA\Property(property: 'product_id', type: 'integer', example: 3),
        new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 1),
    ]
)]
#[OA\Schema(
    schema: 'OrderItemUpdateInput',
    required: ['quantity'],
    properties: [
        new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 2),
    ]
)]
#[OA\Schema(
    schema: 'OrderItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'order_id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'quantity', type: 'integer', example: 1),
        new OA\Property(property: 'unit_price', type: 'string', example: '24.90', description: 'Price of the product when it was ordered'),
        new OA\Property(property: 'subtotal', type: 'string', example: '24.90'),
        new OA\Property(property: 'product_name', type: 'string', example: 'Premium Dog Food 3kg'),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'https://images.unsplash.com/photo-1714068691210-073dc52c6c1d'),
    ]
)]
#[OA\Schema(
    schema: 'ContactInput',
    required: ['name', 'email', 'subject', 'message'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: null, description: 'Optional, for messages from a registered user'),
        new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 2, example: 'Lejla Begić'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lejla.begic@example.com'),
        new OA\Property(property: 'subject', type: 'string', maxLength: 150, minLength: 3, example: 'Product question'),
        new OA\Property(property: 'message', type: 'string', maxLength: 2000, minLength: 10, example: 'Is the Cozy Pet Bed suitable for a large dog?'),
    ]
)]
#[OA\Schema(
    schema: 'Contact',
    allOf: [new OA\Schema(ref: '#/components/schemas/ContactInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-30 12:00:00'),
    ]
)]
#[OA\Schema(
    schema: 'BreedFact',
    properties: [
        new OA\Property(property: 'animal', type: 'string', enum: ['dog', 'cat'], example: 'dog'),
        new OA\Property(property: 'breed', type: 'string', example: 'Labrador Retriever'),
        new OA\Property(property: 'temperament', type: 'string', nullable: true, example: 'Kind, Outgoing, Agile, Gentle, Intelligent, Trusting, Even Tempered'),
        new OA\Property(property: 'life_span', type: 'string', nullable: true, example: '10-12 years'),
        new OA\Property(property: 'breed_group', type: 'string', nullable: true, example: 'Sporting'),
        new OA\Property(property: 'origin', type: 'string', nullable: true, example: 'Canada, USA'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Friendly, active retriever bred to work with hunters on land and in water.'),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'https://cdn2.thedogapi.com/images/B1uW7l5VX.jpg'),
    ]
)]
#[OA\Schema(
    schema: 'BreedFacts',
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'pet_type', type: 'string', enum: ['dog', 'cat', 'both'], example: 'dog'),
        new OA\Property(property: 'facts', type: 'array', items: new OA\Items(ref: '#/components/schemas/BreedFact')),
    ]
)]
class OpenApiSpec {}
