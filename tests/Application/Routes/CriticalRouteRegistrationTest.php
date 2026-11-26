<?php

declare(strict_types=1);

namespace Tests\Application\Routes;

use App\Application\Actions\Page\ContactPageAction;
use App\Application\Actions\Page\MemberForgotPasswordPageAction;
use App\Application\Actions\Page\MemberLoginPageAction;
use App\Application\Actions\Page\MemberRegisterPageAction;
use App\Application\Actions\Page\MemberResetPasswordPageAction;
use Slim\Routing\Route;
use Tests\TestCase;

class CriticalRouteRegistrationTest extends TestCase
{
    private const MANAGED_ENV_KEYS = ['MEMBER_REGISTRATION_ENABLED', 'APP_LEGACY_MODULES_ENABLED'];

    /** @var array<string, string|null> */
    private array $originalEnv = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (self::MANAGED_ENV_KEYS as $key) {
            $this->originalEnv[$key] = array_key_exists($key, $_ENV) ? (string) $_ENV[$key] : null;
            unset($_ENV[$key]);
        }
    }

    protected function tearDown(): void
    {
        foreach (self::MANAGED_ENV_KEYS as $key) {
            if ($this->originalEnv[$key] === null) {
                unset($_ENV[$key]);
                continue;
            }

            $_ENV[$key] = $this->originalEnv[$key];
        }

        parent::tearDown();
    }

    public function testCriticalPublicRoutesExposeExpectedMethodsAndActions(): void
    {
        $_ENV['MEMBER_REGISTRATION_ENABLED'] = 'true';
        $routes = $this->getAppInstance()->getRouteCollector()->getRoutes();

        $expectedRoutes = [
            '/cadastro' => MemberRegisterPageAction::class,
            '/entrar' => MemberLoginPageAction::class,
            '/esqueci-senha' => MemberForgotPasswordPageAction::class,
            '/redefinir-senha' => MemberResetPasswordPageAction::class,
            '/contato' => ContactPageAction::class,
        ];

        foreach ($expectedRoutes as $pattern => $expectedCallable) {
            $route = $this->findRouteByPattern($routes, $pattern);

            $this->assertInstanceOf(Route::class, $route);
            $this->assertSame(['GET', 'POST'], $route->getMethods());
            $this->assertSame($expectedCallable, $route->getCallable());
        }
    }

    public function testRegistrationActionIsNotWiredByDefault(): void
    {
        $routes = $this->getAppInstance()->getRouteCollector()->getRoutes();
        $route = $this->findRouteByPattern($routes, '/cadastro');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertNotSame(MemberRegisterPageAction::class, $route->getCallable());
    }

    public function testLegacyPanelModulesAreOnlyRegisteredWhenEnabled(): void
    {
        $legacyPatterns = [
            '/painel/eventos',
            '/painel/categorias',
            '/painel/biblioteca/livros',
            '/painel/livraria',
            '/painel/livraria/vendas/nova',
            '/media/livraria/capas/{file}',
            '/membro/eventos/{id}/participacao',
        ];

        $routes = $this->getAppInstance()->getRouteCollector()->getRoutes();
        foreach ($legacyPatterns as $pattern) {
            $this->assertNull($this->findRouteByPattern($routes, $pattern), $pattern . ' should be disabled by default.');
        }
        $this->assertInstanceOf(Route::class, $this->findRouteByPattern($routes, '/painel/usuarios'));

        $_ENV['APP_LEGACY_MODULES_ENABLED'] = 'true';
        $routes = $this->getAppInstance()->getRouteCollector()->getRoutes();
        foreach ($legacyPatterns as $pattern) {
            $this->assertInstanceOf(Route::class, $this->findRouteByPattern($routes, $pattern), $pattern . ' should be enabled by flag.');
        }
    }

    /**
     * @param array<string, Route> $routes
     */
    private function findRouteByPattern(array $routes, string $pattern): ?Route
    {
        foreach ($routes as $route) {
            if ($route->getPattern() === $pattern) {
                return $route;
            }
        }

        return null;
    }
}
