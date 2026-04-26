<?php

declare(strict_types=1);

namespace App\Application\ResponseEmitter;

use Psr\Http\Message\ResponseInterface;
use Slim\ResponseEmitter as SlimResponseEmitter;

class ResponseEmitter extends SlimResponseEmitter
{
    protected function prepareResponse(ResponseInterface $response): ResponseInterface
    {
        // This variable should be set to the allowed host from which your API can be accessed with
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        return $response
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader(
                'Access-Control-Allow-Headers',
                'X-Requested-With, Content-Type, Accept, Origin, Authorization',
            )
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
            ->withHeader('Pragma', 'no-cache');
    }

    /**
     * {@inheritdoc}
     */
    public function emit(ResponseInterface $response): void
    {
        $response = $this->prepareResponse($response);

        if (ob_get_contents()) {
            ob_clean();
        }

        parent::emit($response);
    }
}
