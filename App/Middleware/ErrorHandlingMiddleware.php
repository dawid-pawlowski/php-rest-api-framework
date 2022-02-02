<?php

declare(strict_types = 1);

namespace App\Middleware;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ErrorHandlingMiddleware implements MiddlewareInterface {    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            // TODO: log error
			$response = new Response();
			$response->getBody()->write(json_encode((array)$e));
			return $response->withStatus(500);

        }
    }
}
