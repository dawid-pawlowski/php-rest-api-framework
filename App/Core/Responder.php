<?php declare(strict_types = 1);

namespace App\Core;

use Psr\Http\Message\ResponseInterface;

class Responder {

    private $response;

    public function __construct(ResponseInterface $response) {
        $this->response = $response;
    }
    
    public function response(array $payload): ResponseInterface {
		
		if (array_key_exists('payload', $payload)) {
			$this->response->getBody()->write(json_encode($payload['payload'], JSON_THROW_ON_ERROR));
		}
		
        switch ($payload['status']) {
            case 'UNAUTHORIZED': {
                $this->response = $this->response->withStatus(401);
				break;
            }
            case 'FORBIDDEN': {
                $this->response = $this->response->withStatus(403);
				break;
            }
            case 'NOT_FOUND': {
                $this->response = $this->response->withStatus(404);
				break;
            }
            case 'OK': {
                $this->response = $this->response->withStatus(200);
				break;
            }
            case 'BAD_REQUEST': {
                $this->response = $this->response->withStatus(400);
				break;
            }
            case 'CONFLICT': {
                $this->response = $this->response->withStatus(409);
				break;
            }
            default: {
                $this->response = $this->response->withStatus(500);
				break;
            }
        }
		
		return $this->response
			->withHeader('Content-Type', 'application/json')
			->withHeader('X-Content-Type-Options', 'nosniff');
    }
    
}
