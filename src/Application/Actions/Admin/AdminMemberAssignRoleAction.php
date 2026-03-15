<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use App\Domain\Member\MemberAuthRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Throwable;

class AdminMemberAssignRoleAction extends AbstractPageAction
{
    private MemberAuthRepository $memberAuthRepository;

    public function __construct(LoggerInterface $logger, Twig $twig, MemberAuthRepository $memberAuthRepository)
    {
        parent::__construct($logger, $twig);
        $this->memberAuthRepository = $memberAuthRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $id = (int) ($request->getAttribute('id') ?? 0);
        $body = (array) ($request->getParsedBody() ?? []);
        $roleId = (int) ($body['role_id'] ?? 0);

        if ($id <= 0 || $roleId <= 0) {
            return $response->withHeader('Location', '/painel/usuarios?status=invalid-role')->withStatus(302);
        }

        try {
            $this->memberAuthRepository->approveAndAssignRole($id, $roleId);
        } catch (Throwable $exception) {
            $this->logger->error('Falha ao aprovar/atribuir papel de usuário.', [
                'user_id' => $id,
                'role_id' => $roleId,
                'exception' => $exception,
            ]);

            return $response->withHeader('Location', '/painel/usuarios?status=assign-error')->withStatus(302);
        }

        return $response->withHeader('Location', '/painel/usuarios?status=approved')->withStatus(302);
    }
}
