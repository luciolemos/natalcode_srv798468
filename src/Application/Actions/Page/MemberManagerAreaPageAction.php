<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use App\Domain\Member\MemberAuthRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class MemberManagerAreaPageAction extends AbstractMemberGuardedPageAction
{
    public function __construct(LoggerInterface $logger, Twig $twig, MemberAuthRepository $memberAuthRepository)
    {
        parent::__construct($logger, $twig, $memberAuthRepository);
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $member = $this->resolveAuthenticatedMember($response, true);

        if ($member instanceof Response) {
            return $member;
        }

        $forbiddenResponse = $this->ensureMinimumRole($response, $member, 'manager');
        if ($forbiddenResponse instanceof Response) {
            return $forbiddenResponse;
        }

        return $this->renderPage($response, 'pages/member-manager.twig', [
            'member_data' => $member,
            'page_title' => 'Área de Gestão | CEDE',
            'page_url' => 'https://cedern.org/membro/gestao',
            'page_description' => 'Área interna disponível para gerente e administrador.',
        ]);
    }
}
