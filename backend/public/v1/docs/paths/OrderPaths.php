<?php
use OpenApi\Attributes as OA;

class OrderPaths
{
    #[OA\Get(
        path: '/orders',
        tags: ['orders'],
        summary: 'List all orders, newest first',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of orders',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Order'))
            ),
        ]
    )]
    public function getOrders() {}

    #[OA\Get(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Get one order with its items',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'The order', content: new OA\JsonContent(ref: '#/components/schemas/OrderWithItems')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getOrderById() {}

    #[OA\Post(
        path: '/orders',
        tags: ['orders'],
        summary: 'Place an order (prices come from the database, stock is reduced, payment is cash on delivery)',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created order', content: new OA\JsonContent(ref: '#/components/schemas/OrderWithItems')),
            new OA\Response(response: 400, description: 'Invalid data, unknown user or product', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Not enough stock', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function postOrder() {}

    #[OA\Put(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Update the status or delivery details of an order (pending -> shipped -> delivered, pending or shipped -> cancelled; cancelling returns the stock)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 2, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderUpdateInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated order', content: new OA\JsonContent(ref: '#/components/schemas/OrderWithItems')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Status change not allowed', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function putOrder() {}

    #[OA\Delete(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Delete a delivered or cancelled order together with its items',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'The order is still pending or shipped', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function deleteOrder() {}
}
