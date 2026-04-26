<?php

declare(strict_types=1);

namespace Tests\Application\Handlers;

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Psr7\Response;
use Tests\TestCase;

class ShutdownHandlerTest extends TestCase
{
    public function testInvokesErrorHandlerAndEmitterForFatalError(): void
    {
        $request = $this->createRequest('GET', '/api/fatal');
        $response = new Response(500);

        $errorHandler = $this->getMockBuilder(HttpErrorHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__invoke'])
            ->getMock();

        $errorHandler
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $request,
                $this->callback(static function ($exception): bool {
                    return $exception instanceof HttpInternalServerErrorException
                        && str_contains($exception->getMessage(), 'FATAL ERROR: Fatal test error.');
                }),
                true,
                false,
                false
            )
            ->willReturn($response);

        $shutdown = new class ($request, $errorHandler, true) extends ShutdownHandler {
            /** @var array{type:int,message:string,file:string,line:int}|null */
            public ?array $mockLastError = null;
            public ?ResponseInterface $emittedResponse = null;

            protected function getLastError(): ?array
            {
                return $this->mockLastError;
            }

            protected function createResponseEmitter(): ResponseEmitter
            {
                return new class ($this) extends ResponseEmitter {
                    private object $owner;

                    public function __construct(object $owner)
                    {
                        $this->owner = $owner;
                    }

                    public function emit(ResponseInterface $response): void
                    {
                        $this->owner->emittedResponse = $response;
                    }
                };
            }
        };

        $shutdown->mockLastError = [
            'type' => E_USER_ERROR,
            'message' => 'Fatal test error.',
            'file' => '/tmp/test.php',
            'line' => 99,
        ];

        $shutdown();

        $this->assertSame($response, $shutdown->emittedResponse);
    }

    public function testIgnoresNonFatalErrorTypes(): void
    {
        $request = $this->createRequest('GET', '/api/nao-fatal');

        $errorHandler = $this->getMockBuilder(HttpErrorHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__invoke'])
            ->getMock();

        $errorHandler
            ->expects($this->never())
            ->method('__invoke');

        $shutdown = new class ($request, $errorHandler, false) extends ShutdownHandler {
            /** @var array{type:int,message:string,file:string,line:int}|null */
            public ?array $mockLastError = null;
            public bool $emitCalled = false;

            protected function getLastError(): ?array
            {
                return $this->mockLastError;
            }

            protected function createResponseEmitter(): ResponseEmitter
            {
                return new class ($this) extends ResponseEmitter {
                    private object $owner;

                    public function __construct(object $owner)
                    {
                        $this->owner = $owner;
                    }

                    public function emit(ResponseInterface $response): void
                    {
                        $this->owner->emitCalled = true;
                    }
                };
            }
        };

        $shutdown->mockLastError = [
            'type' => E_WARNING,
            'message' => 'Non fatal warning.',
            'file' => '/tmp/test.php',
            'line' => 20,
        ];

        $shutdown();

        $this->assertFalse($shutdown->emitCalled);
    }
}
