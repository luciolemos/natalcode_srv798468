<?php

declare(strict_types=1);

namespace Tests\Application\ResponseEmitter;

use App\Application\ResponseEmitter\ResponseEmitter;
use Slim\Psr7\Response;
use Tests\TestCase;

class ResponseEmitterTest extends TestCase
{
    public function testPrepareResponseAddsCorsAndCacheHeaders(): void
    {
        $_SERVER['HTTP_ORIGIN'] = 'https://natalcode.com.br';
        $response = new Response(200);

        $emitter = new class () extends ResponseEmitter {
            public function prepare(Response $response): Response
            {
                return $this->prepareResponse($response);
            }
        };

        $prepared = $emitter->prepare($response);

        $this->assertSame('true', $prepared->getHeaderLine('Access-Control-Allow-Credentials'));
        $this->assertSame('https://natalcode.com.br', $prepared->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertSame('GET, POST, PUT, PATCH, DELETE, OPTIONS', $prepared->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertStringContainsString('no-store', strtolower($prepared->getHeaderLine('Cache-Control')));
        $this->assertSame('no-cache', strtolower($prepared->getHeaderLine('Pragma')));

        unset($_SERVER['HTTP_ORIGIN']);
    }

    public function testEmitClearsBufferBeforeWritingBody(): void
    {
        $_SERVER['HTTP_ORIGIN'] = 'https://natalcode.com.br';

        $response = new Response(200);
        $response->getBody()->write('ok');

        $emitter = new ResponseEmitter();

        ob_start();
        echo 'buffer-noise';
        $emitter->emit($response);
        $output = ob_get_clean();

        $this->assertSame('ok', $output);

        unset($_SERVER['HTTP_ORIGIN']);
    }
}
