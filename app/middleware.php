<?php

declare(strict_types=1);

use App\Domain\Agenda\AgendaRepository;
use App\Application\Middleware\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Views\Twig;

return function (App $app) {
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

        $twigEnvironment->addGlobal('current_path', $request->getUri()->getPath());
        $twigEnvironment->addGlobal('dashboard_user', (string) ($_SESSION['admin_user'] ?? ''));
        $twigEnvironment->addGlobal('dashboard_is_authenticated', !empty($_SESSION['admin_authenticated']));
        $twigEnvironment->addGlobal('dashboard_env_label', $dashboardEnvLabel);
        $twigEnvironment->addGlobal('dashboard_env_tone', $dashboardEnvTone);

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

    $app->add(SessionMiddleware::class);
};
