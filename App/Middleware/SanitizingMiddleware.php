<?php declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SanitizingMiddleware implements MiddlewareInterface {
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		return $handler->handle($request->withParsedBody($this->sanitize($request->getParsedBody())));
    }
	
	private function sanitize(array $input, array $fields = [], bool $utf8_encode = true): array {
		
		if (empty($fields)) {
            $fields = array_keys($input);
        }
		
		$return = [];

		foreach ($fields as $field) {
			if (!isset($input[$field])) {
				continue;
			}

			$value = $input[$field];
			if (is_array($value)) {
				$value = $this->sanitize($value, [], $utf8_encode);
			}
			if (is_string($value)) {
				if (strpos($value, "\r") !== false) {
					$value = trim($value);
				}

				if (function_exists('iconv') && function_exists('mb_detect_encoding') && $utf8_encode) {
					$current_encoding = mb_detect_encoding($value);

					if ($current_encoding !== 'UTF-8' && $current_encoding !== 'UTF-16') {
						$value = iconv($current_encoding, 'UTF-8', $value);
					}
				}

				$value = filter_var($value, FILTER_SANITIZE_STRING);
			}

			$return[$field] = $value;
		}

		return $return;
	}
	
}
