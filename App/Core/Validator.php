<?php declare(strict_types = 1);

namespace App\Core;


class Validator {
	
	private $rules;
	private $errors = [];
	private $callbacks;
	
	public function __construct(array $rules = []) {
		$this->rules = $rules;
	}
	
	private function flatten(array $array): array {
		$return = array();
		array_walk_recursive($array, function($a,$b) use (&$return) { $return[$b] = $a; });
		return $return;
	}

	public function validate(array $inputs): bool {
		$this->errors = [];
		
		if (empty($this->rules)) {
			return true;
		}
		
		// $inputs = $this->flatten($inputs);		
		foreach ($inputs as $input_key => $input_value) {
			
			if (is_array($input_value)) {
				$this->validate($input_value);
			}
			
			if (array_key_exists($input_key, $this->rules)) {
				
				preg_match_all('/(?<key>[a-z0-9]+)[:]?(?<value>[^|:]+)?/', $this->rules[$input_key], $this->callbacks, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);
				
				foreach ($this->callbacks as $callback) {
					if (array_key_exists('key', $callback)) {
						if (method_exists($this, $callback['key'])) {
							if ((is_null($input_value) || $input_value === '') || (is_array($input_value) && count($input_value) === 0) || (!call_user_func_array([$this, $callback['key']], [$input_value, $callback['value']]))) {
								$this->errors[] = $input_key;
								break;
							}
						}
					}
				}
			}
		}
		
		
		if (!empty($this->errors)) {
			return false;
		}
		
		return true;
	}
	
	private function email($value): bool {
		
		$value = filter_var($value, FILTER_SANITIZE_EMAIL);
		
		$exploded = explode("@", $value);
		$check = array_pop($exploded);
		if (filter_var($value, FILTER_VALIDATE_EMAIL) && checkdnsrr(idn_to_ascii($check), "MX")) {
			return true;
		}
		
		return false;
	}

	private function ISOCountryCode($value): bool {
        return preg_match("/^[A-Z]{2}$/i", $value) ? true : false;
    }

	private function guidv4($value): bool {
        return preg_match("/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i", $value) ? true : false;
    }
	
	private function required($value): bool {
        return (!is_null($value) && $value !== '') || (is_array($value) && count($value) !== 0);
    }

    private function json($value): bool {
        if (!is_string($input) || $input === '') {
            return false;
        }

        json_decode($input);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function integer($value): bool {
        if (is_int($value)) {
            return true;
        }

        return ctype_digit($value);
    }

	private function minimum($value, $limit): bool {
		return $value >= $limit;
	}
	
	private function maximum($value, $limit): bool {
		return $value <= $limit;
	}

	private function base64($value) :bool {
        if (!is_string($value)) {
            return false;
        }

        if (!preg_match('#^[A-Za-z0-9+/\n\r]+={0,2}$#', $value)) {
            return false;
        }

        return mb_strlen($input) % 4 === 0;
    }
	
	public function getErrors(): array {
		return $this->errors;
	}
	
}
