<?php

declare(strict_types = 1);

namespace App\Middleware;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Core\Validator;

class ValidatingMiddleware implements MiddlewareInterface {
	
	private $validator;
	
	public function __construct(Validator $validator) {
		$this->validator = $validator;
    }
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
				
		if ($this->validator->validate($request->getParsedBody())) {
			return $handler->handle($request);
		}
		
		// TODO: add validation errors to the response
		return (new Response())->withStatus(400);
    }
	
}
