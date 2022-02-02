<?php

declare(strict_types = 1);

namespace App\Core;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DefaultHandler implements RequestHandlerInterface {
	
	public function __construct(int $defaultStatus = 500) {
		$this->defaultStatus = $defaultStatus;
	}
	
    public function handle(ServerRequestInterface $request): ResponseInterface {
        return (new Response())->withStatus($this->defaultStatus);
    }
}
