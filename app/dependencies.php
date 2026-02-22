<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        Twig::class => function () {
            $twig = Twig::create(__DIR__ . '/../templates', [
                'cache' => false,
            ]);

            $allowedThemes = ['blue', 'red', 'green', 'violet', 'amber'];
            $allowedModes = ['light', 'dark'];
            $allowedIntensities = ['neutral', 'vivid'];

            $defaultTheme = strtolower((string) ($_ENV['APP_DEFAULT_THEME'] ?? 'amber'));
            $defaultMode = strtolower((string) ($_ENV['APP_DEFAULT_MODE'] ?? 'light'));
            $defaultDarkIntensity = strtolower((string) ($_ENV['APP_DEFAULT_DARK_INTENSITY'] ?? 'neutral'));

            if (!in_array($defaultTheme, $allowedThemes, true)) {
                $defaultTheme = 'amber';
            }
            if (!in_array($defaultMode, $allowedModes, true)) {
                $defaultMode = 'light';
            }
            if (!in_array($defaultDarkIntensity, $allowedIntensities, true)) {
                $defaultDarkIntensity = 'neutral';
            }

            $twig->getEnvironment()->addGlobal('default_theme', $defaultTheme);
            $twig->getEnvironment()->addGlobal('default_mode', $defaultMode);
            $twig->getEnvironment()->addGlobal('default_dark_intensity', $defaultDarkIntensity);

            return $twig;
        },
    ]);
};
