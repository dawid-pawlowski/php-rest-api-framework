<?php declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Core\Generator;

class GUIDv4Middleware implements MiddlewareInterface {

	private $gemerator;
	
	public function __construct(Generator $generator) {
		$this->generator = $generator;
    }
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {		
		return $handler->handle($request->withAttribute('guid', $this->generator->guid()));
    }
	
}
