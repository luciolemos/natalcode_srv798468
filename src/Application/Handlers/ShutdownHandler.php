<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\ResponseEmitter\ResponseEmitter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class ShutdownHandler
{
    private const FATAL_ERRORS = [
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
    ];

    private Request $request;

    private HttpErrorHandler $errorHandler;

    private bool $displayErrorDetails;

    public function __construct(
        Request $request,
        HttpErrorHandler $errorHandler,
        bool $displayErrorDetails
    ) {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
    }

    public function __invoke(): void
    {
        $error = $this->getLastError();
        if ($error && in_array($error['type'], self::FATAL_ERRORS, true)) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        $message = "FATAL ERROR: {$errorMessage}. ";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;

                    case E_USER_WARNING:
                        $message = "WARNING: {$errorMessage}";
                        break;

                    case E_USER_NOTICE:
                        $message = "NOTICE: {$errorMessage}";
                        break;

                    default:
                        $message = "ERROR: {$errorMessage}";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                }
            }

            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = $this->errorHandler->__invoke(
                $this->request,
                $exception,
                $this->displayErrorDetails,
                false,
                false,
            );

            $responseEmitter = $this->createResponseEmitter();
            $responseEmitter->emit($response);
        }
    }

    /**
     * @return array{type:int,message:string,file:string,line:int}|null
     */
    protected function getLastError(): ?array
    {
        $error = error_get_last();

        return is_array($error) ? $error : null;
    }

    protected function createResponseEmitter(): ResponseEmitter
    {
        return new ResponseEmitter();
    }
}
