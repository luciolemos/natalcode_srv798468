<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege ambientes que não são de produção.
 *
 * - Fora de produção, toda resposta leva "X-Robots-Tag: noindex" (o Google não indexa o dev).
 * - Com ACCESS_GUARD_USER e ACCESS_GUARD_PASS no .env, o site inteiro pede login HTTP Basic
 *   (exceto /up, usado em monitoramento). Em produção, deixe as duas variáveis vazias.
 */
class EnvironmentGuard
{
    private const ROBOTS = 'noindex, nofollow, noarchive';

    public function handle(Request $request, Closure $next): Response
    {
        $user = (string) config('app.access_guard.user');
        $pass = (string) config('app.access_guard.pass');

        if ($user !== '' && $pass !== '' && ! $request->is('up') && ! $this->authorized($request, $user, $pass)) {
            return response('Acesso restrito.', 401, [
                'WWW-Authenticate' => 'Basic realm="NatalCode (ambiente restrito)", charset="UTF-8"',
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Cache-Control' => 'no-store',
                'X-Robots-Tag' => self::ROBOTS,
            ]);
        }

        $response = $next($request);

        if (! app()->isProduction()) {
            $response->headers->set('X-Robots-Tag', self::ROBOTS);
        }

        return $response;
    }

    private function authorized(Request $request, string $user, string $pass): bool
    {
        $givenUser = (string) $request->getUser();
        $givenPass = (string) $request->getPassword();

        // Compara os dois campos sempre, em tempo constante.
        $userOk = hash_equals($user, $givenUser);
        $passOk = hash_equals($pass, $givenPass);

        return $userOk && $passOk;
    }
}
