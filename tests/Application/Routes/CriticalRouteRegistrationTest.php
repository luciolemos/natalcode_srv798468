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
    public function testCriticalPublicRoutesExposeExpectedMethodsAndActions(): void
    {
        $app = $this->getAppInstance();
        $routes = $app->getRouteCollector()->getRoutes();

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
