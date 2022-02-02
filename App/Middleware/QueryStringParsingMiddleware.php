<?php declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Laminas\Diactoros\Response;

class QueryStringParsingMiddleware implements MiddlewareInterface {
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

		$postBody = $request->getParsedBody();
		$queryBody = $request->getQueryParams();
		
		$mergedBody = array_merge($postBody, $queryBody);
		$request = $request->withParsedBody($mergedBody);
		
		return $handler->handle($request);
    }
}
