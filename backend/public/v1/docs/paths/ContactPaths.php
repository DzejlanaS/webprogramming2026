<?php
use OpenApi\Attributes as OA;

class ContactPaths
{
    #[OA\Get(
        path: '/contacts',
        tags: ['contacts'],
        summary: 'List contact messages, newest first, optionally only those of one user',
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, description: 'Only messages of this user', example: 2, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of contact messages',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Contact'))
            ),
            new OA\Response(response: 400, description: 'Invalid user_id', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'User not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getContacts() {}

    #[OA\Get(
        path: '/contacts/{id}',
        tags: ['contacts'],
        summary: 'Get one contact message',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'The contact message', content: new OA\JsonContent(ref: '#/components/schemas/Contact')),
            new OA\Response(response: 404, description: 'Contact message not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function getContactById() {}

    #[OA\Post(
        path: '/contacts',
        tags: ['contacts'],
        summary: 'Send a contact message',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ContactInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created contact message', content: new OA\JsonContent(ref: '#/components/schemas/Contact')),
            new OA\Response(response: 400, description: 'Invalid data or unknown user', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function postContact() {}

    #[OA\Put(
        path: '/contacts/{id}',
        tags: ['contacts'],
        summary: 'Update a contact message (fields that are left out keep their value)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ContactInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated contact message', content: new OA\JsonContent(ref: '#/components/schemas/Contact')),
            new OA\Response(response: 400, description: 'Invalid data or unknown user', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Contact message not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function putContact() {}

    #[OA\Delete(
        path: '/contacts/{id}',
        tags: ['contacts'],
        summary: 'Delete a contact message',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Contact message deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Contact message not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function deleteContact() {}
}
