<?php declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Laminas\Diactoros\Response;

class JSONParsingMiddleware implements MiddlewareInterface {
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

		$contentType = $request->getHeaderLine('Content-Type');
		$acceptType  = $request->getHeaderLine('Accept');

        if (strstr($contentType, 'application/json') && strstr($acceptType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
			
			if (json_last_error() !== JSON_ERROR_NONE || $contents === null) {
				$contents = [];
				// return (new Response())->withStatus(400);
			}
			
			$request = $request->withParsedBody($contents);
			return $handler->handle($request);
        }

        return (new Response())->withStatus(406);
    }
}
