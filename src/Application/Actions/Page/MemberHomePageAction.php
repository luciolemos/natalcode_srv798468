<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

use App\Domain\Member\MemberAuthRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class MemberHomePageAction extends AbstractMemberGuardedPageAction
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

        $queryParams = $request->getQueryParams();
        $status = trim((string) ($queryParams['status'] ?? ''));

        return $this->renderPage($response, 'pages/member-home.twig', [
            'member_data' => $member,
            'member_home_status' => $status,
            'page_title' => 'Área do Membro | CEDE',
            'page_url' => 'https://cedern.org/membro',
            'page_description' => 'Área do membro do CEDE.',
        ]);
    }
}
