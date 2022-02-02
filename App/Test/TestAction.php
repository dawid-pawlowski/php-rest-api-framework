<?php declare(strict_types = 1);

namespace App\Test;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use App\Core\Responder;

class TestAction implements RequestHandlerInterface {
    private $responder;
    private $service;
    
    public function __construct(Responder $responder) {
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $payload = array('status' =>'OK', 'payload' => "Test test test");
        return $this->responder->response($payload);
    }
}
