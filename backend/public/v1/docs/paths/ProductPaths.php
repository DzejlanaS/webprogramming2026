<?php
use OpenApi\Attributes as OA;

class ProductPaths
{
    #[OA\Get(
        path: '/products',
        tags: ['products'],
        summary: 'List products, optionally filtered (filters can be combined)',
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, description: 'Only products of this category', example: 1, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Part of the product name', example: 'food', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(
                name: 'pet_type',
                in: 'query',
                required: false,
                description: 'Products for this animal (products for both animals are included)',
                schema: new OA\Schema(type: 'string', enum: ['dog', 'cat'])
            ),
            new OA\Parameter(name: 'featured', in: 'query', required: false, description: 'true for featured products only', schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of products',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Product'))
            ),
            new OA\Response(response: 400, description: 'Invalid filter', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getProducts() {}

    #[OA\Get(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Get one product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'The product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getProductById() {}

    #[OA\Post(
        path: '/products',
        tags: ['products'],
        summary: 'Create a product',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Invalid data or unknown category', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Already 4 featured products', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function postProduct() {}

    #[OA\Put(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Update a product (fields that are left out keep their value)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Invalid data or unknown category', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'Already 4 featured products', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function putProduct() {}

    #[OA\Delete(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Delete a product that has never been ordered',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 3, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 409, description: 'The product has been ordered', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function deleteProduct() {}
}
