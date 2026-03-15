<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminAgendaDeleteAction extends AbstractAdminAgendaAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $id = (int) ($request->getAttribute('id') ?? 0);

        if ($id <= 0) {
            return $this->redirect($response, '/painel/eventos?status=invalid-id');
        }

        try {
            $this->agendaRepository->deleteEvent($id);
            return $this->redirect($response, '/painel/eventos?status=deleted');
        } catch (\Throwable $exception) {
            $this->logger->warning('Falha ao excluir evento no admin.', [
                'event_id' => $id,
                'error' => $exception->getMessage(),
            ]);

            return $this->redirect($response, '/painel/eventos?status=delete-error');
        }
    }
}
