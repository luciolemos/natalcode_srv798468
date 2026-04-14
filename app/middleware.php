<?php

declare(strict_types=1);

use App\Domain\Agenda\AgendaRepository;
use App\Domain\Analytics\SiteVisitRepository;
use App\Domain\Member\MemberAuthRepository;
use App\Application\Middleware\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Views\Twig;

return function (App $app) {
    $app->add(function (Request $request, RequestHandler $handler) use ($app) {
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $app->getResponseFactory()->createResponse(204);
        }

        return $handler->handle($request);
    });

    $normalizeTrackedPageKey = static function (string $path): string {
        $normalizedPath = rtrim(trim($path), '/');

        return $normalizedPath === '' ? '/' : $normalizedPath;
    };

    $isTrackablePublicPage = static function (Request $request) use ($normalizeTrackedPageKey): bool {
        if (strtoupper($request->getMethod()) !== 'GET') {
            return false;
        }

        $path = $normalizeTrackedPageKey($request->getUri()->getPath());

        if ($path === '/entrar' || $path === '/cadastro') {
            return false;
        }

        if (
            preg_match('#^/(painel|membro|admin|assets)(/|$)#', $path) === 1
            || str_ends_with($path, '/ics')
            || preg_match('/\.[a-z0-9]{2,8}$/i', $path) === 1
        ) {
            return false;
        }

        return true;
    };

    $buildVisitorCookieHeader = static function (string $name, string $value, int $maxAge): string {
        $expires = gmdate('D, d M Y H:i:s \G\M\T', time() + $maxAge);
        $cookieHeader = sprintf(
            '%s=%s; Path=/; Max-Age=%d; Expires=%s; HttpOnly; SameSite=Lax',
            rawurlencode($name),
            rawurlencode($value),
            $maxAge,
            $expires
        );

        $isHttps = str_starts_with(strtolower((string) ($_ENV['APP_DEFAULT_PAGE_URL'] ?? 'https://natalcode.com.br/')), 'https://')
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https'
            || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off');

        if ($isHttps) {
            $cookieHeader .= '; Secure';
        }

        return $cookieHeader;
    };

    $cookieName = 'natalcode_vid';
    $cookieMaxAge = 31536000;

    $buildSecurityHeaders = static function (Request $request): array {
        $uriScheme = strtolower($request->getUri()->getScheme());
        $forwardedProto = strtolower(trim((string) $request->getHeaderLine('X-Forwarded-Proto')));
        $serverHttps = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
        $isHttps = $uriScheme === 'https'
            || $forwardedProto === 'https'
            || ($serverHttps !== '' && $serverHttps !== 'off');

        $cspDirectives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "img-src 'self' data: https:",
            "font-src 'self' data: https://fonts.gstatic.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://www.googletagmanager.com",
            "connect-src 'self' https://www.google.com https://www.gstatic.com https://www.google-analytics.com https://region1.google-analytics.com",
            "frame-src 'self' https://www.google.com https://www.gstatic.com https://recaptcha.google.com https://www.googletagmanager.com",
        ];

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=()',
            'Content-Security-Policy' => implode('; ', $cspDirectives),
        ];

        if ($isHttps) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        return $headers;
    };

    $app->add(function (
        Request $request,
        RequestHandler $handler
    ) use (
        $app,
        $buildVisitorCookieHeader,
        $cookieName,
        $cookieMaxAge,
        $isTrackablePublicPage,
        $normalizeTrackedPageKey
    ) {
        $response = $handler->handle($request);

        if (!$isTrackablePublicPage($request)) {
            return $response;
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            return $response;
        }

        $contentType = strtolower(implode(';', $response->getHeader('Content-Type')));
        if ($contentType !== '' && !str_contains($contentType, 'text/html')) {
            return $response;
        }

        $visitorToken = trim((string) ($_COOKIE[$cookieName] ?? ''));
        $shouldSetVisitorCookie = false;
        if (!preg_match('/^[a-f0-9]{32}$/i', $visitorToken)) {
            try {
                $visitorToken = bin2hex(random_bytes(16));
                $shouldSetVisitorCookie = true;
            } catch (\Throwable $exception) {
                return $response;
            }
        }

        $pageKey = $normalizeTrackedPageKey($request->getUri()->getPath());
        $visitorTokenHash = hash('sha256', $visitorToken);
        $trackingKey = $pageKey . '|' . substr($visitorTokenHash, 0, 16);
        $trackedVisits = $_SESSION['_site_visit_tracker'] ?? [];

        if (!is_array($trackedVisits)) {
            $trackedVisits = [];
        }

        $now = time();
        $lastTrackedAt = (int) ($trackedVisits[$trackingKey] ?? 0);

        if ($lastTrackedAt === 0 || ($now - $lastTrackedAt) >= 60) {
            try {
                /** @var SiteVisitRepository $siteVisitRepository */
                $siteVisitRepository = $app->getContainer()->get(SiteVisitRepository::class);
                $siteVisitRepository->registerPageVisit($pageKey, $visitorTokenHash, new \DateTimeImmutable('now'));
                $trackedVisits[$trackingKey] = $now;
                $_SESSION['_site_visit_tracker'] = $trackedVisits;
            } catch (\Throwable $exception) {
            }
        }

        if ($shouldSetVisitorCookie) {
            $response = $response->withAddedHeader(
                'Set-Cookie',
                $buildVisitorCookieHeader($cookieName, $visitorToken, $cookieMaxAge)
            );
        }

        return $response;
    });

    $app->add(function (Request $request, RequestHandler $handler) use ($app) {
        $twig = $app->getContainer()->get(Twig::class);
        $twigEnvironment = $twig->getEnvironment();
        $appEnv = strtolower(trim((string) ($_ENV['APP_ENV'] ?? 'production')));
        $dashboardEnvLabel = 'Produção';
        $dashboardEnvTone = 'prod';

        if (in_array($appEnv, ['dev', 'development', 'local'], true)) {
            $dashboardEnvLabel = 'Desenvolvimento';
            $dashboardEnvTone = 'dev';
        } elseif (in_array($appEnv, ['test', 'testing', 'qa', 'homolog'], true)) {
            $dashboardEnvLabel = 'Homologação';
            $dashboardEnvTone = 'test';
        }

        $dashboardRoleWeights = [
            'member' => 10,
            'operator' => 20,
            'manager' => 30,
            'admin' => 40,
        ];

        $memberRoleKey = trim((string) ($_SESSION['member_role_key'] ?? 'member'));
        $memberRoleWeight = (int) ($dashboardRoleWeights[$memberRoleKey] ?? 0);
        $memberHasDashboardAccess = !empty($_SESSION['member_authenticated'])
            && ($memberRoleWeight >= 20 || $memberRoleKey === 'bookshop_operator');
        $memberCanManageUsers = !empty($_SESSION['member_authenticated'])
            && in_array($memberRoleKey, ['admin'], true);

        $dashboardIsAdminSession = !empty($_SESSION['admin_authenticated']);
        $dashboardIsAuthenticated = !empty($_SESSION['admin_authenticated']) || $memberHasDashboardAccess;
        $dashboardCanManageUsers = $dashboardIsAdminSession || $memberCanManageUsers;
        $dashboardUser = (string) ($_SESSION['admin_user'] ?? '');
        $dashboardUserPhotoPath = '';
        $dashboardAdminNotifications = [];
        $dashboardPendingUsers = [];
        $dashboardNotificationCount = 0;

        if ($dashboardUser === '' && $memberHasDashboardAccess) {
            $dashboardUser = (string) ($_SESSION['member_name'] ?? 'Usuário');
            $dashboardUserPhotoPath = (string) ($_SESSION['member_profile_photo_path'] ?? '');
        }

        if ($dashboardCanManageUsers) {
            try {
                /** @var MemberAuthRepository $memberAuthRepository */
                $memberAuthRepository = $app->getContainer()->get(MemberAuthRepository::class);
                $allUsers = $memberAuthRepository->findAllUsersForAdmin();

                $dashboardPendingUsers = array_values(array_filter(
                    $allUsers,
                    static fn (array $user): bool => (string) ($user['status'] ?? '') === 'pending'
                ));

                $dashboardNotificationCount = count($dashboardPendingUsers);

                if ($dashboardNotificationCount > 0) {
                    $dashboardAdminNotifications[] = [
                        'title' => 'Contas pendentes',
                        'description' => $dashboardNotificationCount . ' cadastro(s) para aprovar.',
                        'href' => '/painel/usuarios?sort=created_at&dir=desc&q=pending',
                        'cta' => 'Aprovar contas',
                    ];
                }

                $dashboardPendingUsers = array_slice($dashboardPendingUsers, 0, 5);
            } catch (\Throwable $exception) {
            }
        }

        $twigEnvironment->addGlobal('current_path', $request->getUri()->getPath());
        $twigEnvironment->addGlobal('dashboard_user', $dashboardUser);
        $twigEnvironment->addGlobal('dashboard_user_photo_path', $dashboardUserPhotoPath);
        $twigEnvironment->addGlobal('dashboard_is_authenticated', $dashboardIsAuthenticated);
        $twigEnvironment->addGlobal('dashboard_is_admin_session', $dashboardIsAdminSession);
        $twigEnvironment->addGlobal('member_is_authenticated', !empty($_SESSION['member_authenticated']));
        $twigEnvironment->addGlobal('member_name', (string) ($_SESSION['member_name'] ?? ''));
        $twigEnvironment->addGlobal('member_role_key', (string) ($_SESSION['member_role_key'] ?? ''));
        $twigEnvironment->addGlobal('member_role_name', (string) ($_SESSION['member_role_name'] ?? 'Membro'));
        $twigEnvironment->addGlobal(
            'member_profile_photo_path',
            (string) ($_SESSION['member_profile_photo_path'] ?? '')
        );
        $twigEnvironment->addGlobal('dashboard_env_label', $dashboardEnvLabel);
        $twigEnvironment->addGlobal('dashboard_env_tone', $dashboardEnvTone);
        $twigEnvironment->addGlobal('dashboard_admin_notifications', $dashboardAdminNotifications);
        $twigEnvironment->addGlobal('dashboard_admin_pending_users', $dashboardPendingUsers);
        $twigEnvironment->addGlobal('dashboard_admin_notification_count', $dashboardNotificationCount);

        $navigationMenu = (array) ($twigEnvironment->getGlobals()['navigation_menu'] ?? []);

        if ($navigationMenu !== []) {
            try {
                /** @var AgendaRepository $agendaRepository */
                $agendaRepository = $app->getContainer()->get(AgendaRepository::class);
                $upcomingEvents = $agendaRepository->findUpcomingPublished(4);
                $maxLabelLength = 36;

                $toCompactLabel = static function (string $value) use ($maxLabelLength): string {
                    $label = trim($value);

                    if ($label === '') {
                        return 'Atividade';
                    }

                    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                        if (mb_strlen($label) <= $maxLabelLength) {
                            return $label;
                        }

                        return rtrim(mb_substr($label, 0, $maxLabelLength - 1)) . '…';
                    }

                    if (strlen($label) <= $maxLabelLength) {
                        return $label;
                    }

                    return rtrim(substr($label, 0, $maxLabelLength - 1)) . '…';
                };

                $dynamicAgendaItems = [[
                    'path' => '/agenda',
                    'label' => 'Visão geral',
                ]];

                foreach ($upcomingEvents as $event) {
                    $slug = trim((string) ($event['slug'] ?? ''));
                    $title = trim((string) ($event['title'] ?? 'Atividade'));

                    if ($slug === '') {
                        continue;
                    }

                    $dynamicAgendaItems[] = [
                        'path' => '/agenda/' . $slug,
                        'label' => $toCompactLabel($title),
                    ];
                }

                foreach ($navigationMenu as $index => $group) {
                    if (!is_array($group) || (string) ($group['key'] ?? '') !== 'agenda') {
                        continue;
                    }

                    $navigationMenu[$index]['items'] = $dynamicAgendaItems;
                    break;
                }

                $twigEnvironment->addGlobal('navigation_menu', $navigationMenu);
            } catch (\Throwable $exception) {
            }
        }

        return $handler->handle($request);
    });

    $app->add(function (
        Request $request,
        RequestHandler $handler
    ) use (
        $app
    ) {
        $start = microtime(true);
        $response = $handler->handle($request);

        $path = $request->getUri()->getPath();
        if (preg_match('#^/(assets|favicon|robots|sitemap)(/|$)#', $path) === 1) {
            return $response;
        }

        try {
            $logger = $app->getContainer()->get(LoggerInterface::class);
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $ip = trim((string) $request->getHeaderLine('X-Forwarded-For'));
            $ip = $ip !== '' ? explode(',', $ip)[0] : $request->getServerParams()['REMOTE_ADDR'] ?? '';

            $logger->info('http_request', [
                'method' => $request->getMethod(),
                'path' => $path,
                'status' => $response->getStatusCode(),
                'duration_ms' => $durationMs,
                'query' => (string) $request->getUri()->getQuery(),
                'ip' => trim((string) $ip),
                'ua' => (string) $request->getHeaderLine('User-Agent'),
            ]);
        } catch (\Throwable $exception) {
        }

        return $response;
    });

    $app->add(function (
        Request $request,
        RequestHandler $handler
    ) use (
        $buildSecurityHeaders
    ) {
        $response = $handler->handle($request);
        $contentType = strtolower($response->getHeaderLine('Content-Type'));
        $isHtmlResponse = $contentType === '' || str_contains($contentType, 'text/html');
        $securityHeaders = $buildSecurityHeaders($request);

        foreach ($securityHeaders as $headerName => $headerValue) {
            if ($headerName === 'Content-Security-Policy' && !$isHtmlResponse) {
                continue;
            }

            if ($response->hasHeader($headerName)) {
                continue;
            }

            $response = $response->withHeader($headerName, $headerValue);
        }

        return $response;
    });

    $app->add(SessionMiddleware::class);
};
