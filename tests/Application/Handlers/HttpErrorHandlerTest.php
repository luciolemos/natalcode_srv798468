<?php

declare(strict_types=1);

namespace Tests\Application\Handlers;

use App\Application\Actions\ActionError;
use App\Application\Handlers\HttpErrorHandler;
use RuntimeException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Tests\TestCase;

class HttpErrorHandlerTest extends TestCase
{
    public function testApiPathAlwaysRespondsWithJson(): void
    {
        $app = $this->getAppInstance();
        $handler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory());

        $request = $this->createRequest('GET', '/api/test', ['Accept' => 'text/html']);
        $exception = new HttpNotFoundException($request, 'Recurso nao encontrado');

        $response = $handler($request, $exception, false, false, false);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('application/json', strtolower($response->getHeaderLine('Content-Type')));
        $this->assertIsArray($payload);
        $this->assertSame(ActionError::RESOURCE_NOT_FOUND, $payload['error']['type'] ?? null);
        $this->assertSame('Recurso nao encontrado', $payload['error']['description'] ?? null);
    }

    public function testHtmlRequestRespondsWithEscapedErrorPage(): void
    {
        $app = $this->getAppInstance();
        $handler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory());

        $request = $this->createRequest('GET', '/contato', ['Accept' => 'text/html']);
        $exception = new HttpForbiddenException($request, 'Conteudo <script>alert(1)</script> bloqueado');

        $response = $handler($request, $exception, false, false, false);
        $body = (string) $response->getBody();

        $this->assertSame(403, $response->getStatusCode());
        $this->assertStringContainsString('text/html', strtolower($response->getHeaderLine('Content-Type')));
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $body);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $body);
    }

    public function testDisplayErrorDetailsExposesThrowableMessageOutsideHttpException(): void
    {
        $app = $this->getAppInstance();
        $handler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory());

        $request = $this->createRequest('GET', '/api/falha');
        $exception = new RuntimeException('Falha inesperada na camada de dominio');

        $response = $handler($request, $exception, true, false, false);
        $payload = json_decode((string) $response->getBody(), true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(ActionError::SERVER_ERROR, $payload['error']['type'] ?? null);
        $this->assertSame('Falha inesperada na camada de dominio', $payload['error']['description'] ?? null);
    }
}
