<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminLibraryCategoryToggleStatusAction extends AbstractAdminLibraryAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $id = (int) ($request->getAttribute('id') ?? 0);

        if ($id <= 0) {
            $this->storeSessionFlash(AdminLibraryCategoryListPageAction::FLASH_KEY, [
                'status' => 'not-found',
            ]);

            return $response->withHeader('Location', '/painel/biblioteca/categorias')->withStatus(303);
        }

        try {
            $category = $this->libraryRepository->findCategoryByIdForAdmin($id);

            if ($category === null) {
                $this->storeSessionFlash(AdminLibraryCategoryListPageAction::FLASH_KEY, [
                    'status' => 'not-found',
                ]);

                return $response->withHeader('Location', '/painel/biblioteca/categorias')->withStatus(303);
            }

            $currentIsActive = ((int) ($category['is_active'] ?? 0)) === 1;
            $newIsActive = !$currentIsActive;

            $this->libraryRepository->setCategoryActive($id, $newIsActive);

            $this->storeSessionFlash(AdminLibraryCategoryListPageAction::FLASH_KEY, [
                'status' => $newIsActive ? 'enabled' : 'disabled',
            ]);

            return $response->withHeader('Location', '/painel/biblioteca/categorias')->withStatus(303);
        } catch (\Throwable $exception) {
            $this->logger->warning('Falha ao alternar status da categoria da biblioteca no admin.', [
                'category_id' => $id,
                'error' => $exception->getMessage(),
            ]);

            $this->storeSessionFlash(AdminLibraryCategoryListPageAction::FLASH_KEY, [
                'status' => 'toggle-error',
            ]);

            return $response->withHeader('Location', '/painel/biblioteca/categorias')->withStatus(303);
        }
    }
}
