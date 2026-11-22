<?php
use OpenApi\Attributes as OA;

class OrderItemPaths
{
    #[OA\Get(
        path: '/order-items',
        tags: ['order-items'],
        summary: 'List order items, optionally only those of one order',
        parameters: [
            new OA\Parameter(name: 'order_id', in: 'query', required: false, description: 'Only items of this order', example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of order items',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderItem'))
            ),
            new OA\Response(response: 400, description: 'Invalid order_id', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getOrderItems() {}

    #[OA\Get(
        path: '/order-items/{id}',
        tags: ['order-items'],
        summary: 'Get one order item',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'The order item', content: new OA\JsonContent(ref: '#/components/schemas/OrderItem')),
            new OA\Response(response: 404, description: 'Order item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getOrderItemById() {}

    #[OA\Post(
        path: '/order-items',
        tags: ['order-items'],
        summary: 'Add a product to a pending order (the price comes from the database, stock and order total are updated)',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderItemInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created order item', content: new OA\JsonContent(ref: '#/components/schemas/OrderItem')),
            new OA\Response(response: 400, description: 'Invalid data, unknown order or product', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(
                response: 409,
                description: 'Order is not pending, product already in the order or not enough stock',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
        ]
    )]
    public function postOrderItem() {}

    #[OA\Put(
        path: '/order-items/{id}',
        tags: ['order-items'],
        summary: 'Change the quantity of an item in a pending order (stock and order total are updated)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 3, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderItemUpdateInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated order item', content: new OA\JsonContent(ref: '#/components/schemas/OrderItem')),
            new OA\Response(response: 400, description: 'Invalid quantity', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Order is not pending or not enough stock', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function putOrderItem() {}

    #[OA\Delete(
        path: '/order-items/{id}',
        tags: ['order-items'],
        summary: 'Remove an item from a pending order (stock is returned, order total is updated)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 3, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order item deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Order item not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Order is not pending or this is its last item', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function deleteOrderItem() {}
}
