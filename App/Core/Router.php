<?php

declare(strict_types = 1);

namespace App\Core;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class Router {
    private $routes;
    private $container;
    private $handler;
	private $params;

    public function __construct(ContainerInterface $container, array $routes) {
        $this->routes = $routes;
        $this->container = $container;
		$this->params = [];
    }
    
    public function matches(ServerRequestInterface $request): bool {    
        foreach ($this->routes as $route) {
			
            list($method, $pattern, $handler) = $route;

            if ($request->getMethod() !== $method) {
                continue;
            }
			
            if (!preg_match('#^' . $pattern . '$#', $request->getUri()->getPath(), $this->params)) {
                continue;
            }
			
			array_shift($this->params);
            
            $this->handler = $this->container->get($handler);
            
            return true;
        }
        
        return false;
    }
    
    public function dispatch(ServerRequestInterface $request): ResponseInterface {
        return $this->handler->handle($request->withAttribute('route-params', $this->params));
    }
}
