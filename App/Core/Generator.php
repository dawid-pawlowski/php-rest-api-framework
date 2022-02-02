<?php declare(strict_types = 1);

namespace App\Core;

use App\Exception\GUIDv4GeneratorException;

class Generator {
	
	public function guid(): string {
		if (function_exists('com_create_guid') === true) {
			return trim(com_create_guid(), '{}');
		}
		
		if (function_exists('openssl_random_pseudo_bytes') === true) {
			$data = openssl_random_pseudo_bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}
		
		throw new GUIDv4GeneratorException('No GUIDv4 generation function available');
	}

    public function randomToken(int $length = 26, string $alphanum = 'abcdefghijklmnopqrstuvwxyz1234567ABCDEFGHIJKLMNOPQRSTUVWXYZ'): string {
        $str = '';
        $alphamax = strlen($alphanum) - 1;

        for ($i = 0; $i < $length; ++$i) {
            $str .= $alphanum[random_int(0, $alphamax)];
        }

        return $str;
    }
}
