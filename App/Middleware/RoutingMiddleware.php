<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Core\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface {
    private $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
	
        if ($this->router->matches($request)) {
            return $this->router->dispatch($request);
        }
        
        return $handler->handle($request);
    }
}
