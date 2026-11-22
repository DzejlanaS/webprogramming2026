<?php
use OpenApi\Attributes as OA;

class BreedFactPaths
{
    #[OA\Get(
        path: '/products/{id}/breed-facts',
        tags: ['breed-facts'],
        summary: 'Random breed facts for the animal a product is made for (TheDogAPI for dogs, TheCatAPI for cats, both for products made for both)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Breed facts', content: new OA\JsonContent(ref: '#/components/schemas/BreedFacts')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'TheDogAPI and TheCatAPI are not available', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getProductBreedFacts() {}
}
