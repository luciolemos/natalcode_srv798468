<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class MemberLogoutAction extends AbstractPageAction
{
    public function __construct(LoggerInterface $logger, Twig $twig)
    {
        parent::__construct($logger, $twig);
    }

    public function __invoke(Request $request, Response $response): Response
    {
        unset(
            $_SESSION['member_authenticated'],
            $_SESSION['member_user_id'],
            $_SESSION['member_name'],
            $_SESSION['member_email'],
            $_SESSION['member_role_key'],
            $_SESSION['member_role_name'],
            $_SESSION['member_profile_photo_path']
        );

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
