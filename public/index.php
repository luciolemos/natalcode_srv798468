<?php

declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoloadPath)) {
    error_log('[natalcode bootstrap] Missing vendor/autoload.php. Run composer install --no-dev.');
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Application dependencies are missing. Run composer install --no-dev.';
    exit(1);
}

require $autoloadPath;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$projectRoot = dirname(__DIR__);
if (is_file($projectRoot . '/.env')) {
    Dotenv::createImmutable($projectRoot)->safeLoad();
}

if (!empty($_ENV['APP_ERROR_LOG'])) {
    ini_set('log_errors', '1');
    ini_set('error_log', (string) $_ENV['APP_ERROR_LOG']);
}

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

$appEnv = strtolower(trim((string) ($_ENV['APP_ENV'] ?? 'production')));
$enableContainerCompilation = !in_array($appEnv, ['dev', 'development', 'local', 'test'], true);

if ($enableContainerCompilation) {
    $cacheDirectory = $projectRoot . '/var/cache';
    $cacheDirectoryReady = is_dir($cacheDirectory) || @mkdir($cacheDirectory, 0775, true);

    if ($cacheDirectoryReady && is_writable($cacheDirectory)) {
        $containerBuilder->enableCompilation($cacheDirectory);
    } else {
        error_log(
            '[natalcode bootstrap] Container compilation disabled: cache directory is not writable: '
            . $cacheDirectory
        );
    }
}

try {
    // Set up settings
    $settings = require __DIR__ . '/../app/settings.php';
    $settings($containerBuilder);

    // Set up dependencies
    $dependencies = require __DIR__ . '/../app/dependencies.php';
    $dependencies($containerBuilder);

    // Set up repositories
    $repositories = require __DIR__ . '/../app/repositories.php';
    $repositories($containerBuilder);

    // Build PHP-DI Container instance
    $container = $containerBuilder->build();

    // Instantiate the app
    AppFactory::setContainer($container);
    $app = AppFactory::create();
    $callableResolver = $app->getCallableResolver();

    // Register middleware
    $middleware = require __DIR__ . '/../app/middleware.php';
    $middleware($app);

    // Register routes
    $routes = require __DIR__ . '/../app/routes.php';
    $routes($app);

    /** @var SettingsInterface $settings */
    $settings = $container->get(SettingsInterface::class);

    $displayErrorDetails = $settings->get('displayErrorDetails');
    $logError = $settings->get('logError');
    $logErrorDetails = $settings->get('logErrorDetails');

    // Create Request object from globals
    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

    // Create Error Handler
    $responseFactory = $app->getResponseFactory();
    $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

    // Create Shutdown Handler
    $shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
    register_shutdown_function($shutdownHandler);

    // Add Routing Middleware
    $app->addRoutingMiddleware();

    // Add Body Parsing Middleware
    $app->addBodyParsingMiddleware();

    // Add Error Middleware
    $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
    $errorMiddleware->setDefaultErrorHandler($errorHandler);

    // Run App & Emit Response
    $response = $app->handle($request);
    $responseEmitter = new ResponseEmitter();
    $responseEmitter->emit($response);
} catch (\Throwable $exception) {
    error_log('[natalcode bootstrap] Unhandled bootstrap exception: ' . $exception::class . ' - ' . $exception->getMessage());

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
    }

    echo 'Internal Server Error';
}
