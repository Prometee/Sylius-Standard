<?php

declare(strict_types=1);

namespace App\OpenApi\Documentation;

use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\DocumentationModifierInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag('sylius.open_api.modifier')]
final class CustomerDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(
        #[Autowire(param: 'sylius.security.api_route')]
        private readonly string $apiRoute
    ) {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        return $this->applyCustomerTokenRefreshDocumentation($docs);
    }

    private function applyCustomerTokenRefreshDocumentation(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['Customer-shop.customer.refresh_token.read'] = [
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $schemas['Customer-shop.customer.refresh_token.read.unauthorized'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'message' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $schemas['Customer-shop.customer.refresh_token.read.bad-request'] = [
            'type' => 'object',
            'properties' => [
                'type' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'title' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'status' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'detail' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $schemas['Customer-shop.customer.refresh_token.create'] = [
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                ],
            ],
        ];

        $components = $components->withSchemas($schemas);
        $docs = $docs->withComponents($components);

        $docs->getPaths()->addPath(
            $this->apiRoute . '/shop/customers/token/refresh',
            new PathItem(
                post: new Operation(
                    operationId: 'postCustomerCredentialsRefreshItem',
                    tags: ['Customer', 'Security'],
                    responses: [
                        Response::HTTP_OK => [
                            'description' => 'JWT token retrieval succeeded',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Customer-shop.customer.refresh_token.read',
                                    ],
                                ],
                            ],
                        ],
                        Response::HTTP_UNAUTHORIZED => [
                            'description' => 'JWT token retrieval failed due to invalid credentials',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Customer-shop.customer.refresh_token.read.unauthorized',
                                    ],
                                ],
                            ],
                        ],
                        Response::HTTP_BAD_REQUEST => [
                            'description' => 'JWT token retrieval failed due to invalid request',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Customer-shop.customer.refresh_token.read.bad-request',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    summary: 'Refresh the JWT token.',
                    requestBody: new RequestBody(
                        description: 'Refresh the JWT token.',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Customer-shop.customer.refresh_token.create',
                                ],
                            ],
                        ]),
                    ),
                ),
            ),
        );

        return $docs;
    }
}
