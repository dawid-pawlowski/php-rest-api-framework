<?php

declare(strict_types = 1);

namespace App\Core;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareWrapper implements RequestHandlerInterface {
    public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $next) {
        $this->middleware = $middleware;
		$this->next = $next;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface {
        return $this->middleware->process($request, $this->next);
    }
}
