<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) use ($app) {
        $twig = $app->getContainer()->get(Twig::class);
        $homeContent = require __DIR__ . '/content/home.php';

        return $twig->render($response, 'home.twig', [
            'homeContent' => $homeContent,
        ]);
    });

    $app->get('/users', function (Request $request, Response $response) use ($app) {
        $twig = $app->getContainer()->get(Twig::class);
        $repository = $app->getContainer()->get(UserRepository::class);
        $users = array_map(
            static fn ($user): array => $user->jsonSerialize(),
            $repository->findAll()
        );

        return $twig->render($response, 'users.twig', ['users' => $users]);
    });

    $app->group('/api/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
