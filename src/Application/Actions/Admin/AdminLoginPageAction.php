<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminLoginPageAction extends AbstractPageAction
{
    private function normalizeEnvValue(string $value): string
    {
        $normalized = trim($value);

        if (strlen($normalized) >= 2) {
            $first = $normalized[0];
            $last = $normalized[strlen($normalized) - 1];

            if (($first === '"' && $last === '"') || ($first === '\'' && $last === '\'')) {
                return substr($normalized, 1, -1);
            }
        }

        return $normalized;
    }

    private function buildDashboardAuthToken(string $username, string $password): string
    {
        $seed = trim((string) ($_ENV['APP_DEFAULT_SITE_NAME'] ?? 'CEDE'));

        return hash('sha256', $seed . '|' . $username . '|' . $password);
    }

    public function __invoke(Request $request, Response $response): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!empty($_SESSION['admin_authenticated'])) {
            return $response->withHeader('Location', '/painel')->withStatus(302);
        }

        $error = '';

        if (strtoupper($request->getMethod()) === 'POST') {
            $body = (array) ($request->getParsedBody() ?? []);
            $username = trim((string) ($body['username'] ?? ''));
            $password = (string) ($body['password'] ?? '');

            $expectedUserRaw = (string) (
                $_ENV['ADMIN_DASHBOARD_USER']
                ?? $_ENV['ADMIN_AGENDA_USER']
                ?? ''
            );
            $expectedPassRaw = (string) (
                $_ENV['ADMIN_DASHBOARD_PASS']
                ?? $_ENV['ADMIN_AGENDA_PASS']
                ?? ''
            );

            $expectedUser = $this->normalizeEnvValue($expectedUserRaw);
            $expectedPass = $this->normalizeEnvValue($expectedPassRaw);

            if ($expectedUser === '' || $expectedPass === '') {
                $error = 'Login do dashboard não configurado no ambiente.';
            } else {
                $isValid = hash_equals($expectedUser, $username)
                    && hash_equals($expectedPass, $password);

                if ($isValid) {
                    $_SESSION['admin_authenticated'] = true;
                    $_SESSION['admin_user'] = $expectedUser;

                    $token = $this->buildDashboardAuthToken($expectedUser, $expectedPass);
                    setcookie('dashboard_auth', $token, [
                        'expires' => time() + (60 * 60 * 8),
                        'path' => '/',
                        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                        'httponly' => true,
                        'samesite' => 'Lax',
                    ]);

                    return $response->withHeader('Location', '/painel')->withStatus(302);
                }

                $error = 'Usuário ou senha inválidos.';
            }
        }

        return $this->renderPage($response, 'pages/admin-login.twig', [
            'admin_login_error' => $error,
            'page_title' => 'Login Dashboard | CEDE',
            'page_url' => 'https://cedern.org/painel/login',
            'page_description' => 'Acesso ao dashboard interno da agenda.',
        ]);
    }
}
