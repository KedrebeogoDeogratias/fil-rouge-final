<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ArrayObject;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Nous étendons la fabrique OpenAPI d'API Platform
        $this->app->extend(OpenApiFactoryInterface::class, function (OpenApiFactoryInterface $factory) {
            return new class($factory) implements OpenApiFactoryInterface {
                public function __construct(private OpenApiFactoryInterface $factory) {}

                public function __invoke(array $context = []): OpenApi
                {
                    // 1. On récupère la documentation générée automatiquement (vos Produits, etc.)
                    $openApi = ($this->factory)($context);

                    // 2. On crée la définition visuelle pour la route /login
                    $loginOperation = new Operation(
                        operationId: 'loginUser',
                        tags: ['Authentification'], // Cela créera une belle catégorie "Authentification" dans Swagger
                        summary: 'Connecter un utilisateur',
                        description: 'Permet de récupérer un token Sanctum.',
                        requestBody: new RequestBody(
                            content: new ArrayObject([
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'email' => ['type' => 'string', 'example' => 'test@exemple.com'],
                                            'password' => ['type' => 'string', 'example' => 'password123'],
                                        ]
                                    ]
                                ]
                            ])
                        ),
                        responses: [
                            '200' => ['description' => 'Token généré avec succès'],
                            '401' => ['description' => 'Identifiants invalides']
                        ]
                    );

                    // 3. On crée la définition visuelle pour la route /register
                    $registerOperation = new Operation(
                        operationId: 'registerUser',
                        tags: ['Authentification'],
                        summary: 'Inscrire un nouvel utilisateur',
                        requestBody: new RequestBody(
                            content: new ArrayObject([
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'name' => ['type' => 'string'],
                                            'email' => ['type' => 'string'],
                                            'password' => ['type' => 'string'],
                                        ]
                                    ]
                                ]
                            ])
                        ),
                        responses: [
                            '201' => ['description' => 'Utilisateur créé'],
                            '400' => ['description' => 'Erreur de validation']
                        ]
                    );

                    // 4. On ajoute nos deux routes manuelles à la documentation
                    $openApi->getPaths()->addPath('/api/login', new PathItem(post: $loginOperation));
                    $openApi->getPaths()->addPath('/api/register', new PathItem(post: $registerOperation));

                    return $openApi;
                }
            };
        });
    }
}