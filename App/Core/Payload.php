<?php

declare(strict_types = 1);

namespace App\Core;

class Payload {

    private $status;
    private $result;
    
    public function __construct(string $status, array $result = []) {
        $this->status = $status;
        $this->result = $result;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    public function getResult(): array {
        return $this->result;
    }
}
