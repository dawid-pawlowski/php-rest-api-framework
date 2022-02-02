<?php

declare(strict_types = 1);

namespace App\Core;

use App\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionClass;

class Container implements ContainerInterface
{
    private $services = [];

    public function get(string $id)
    {		
        $item = $this->resolve($id);
        if (!($item instanceof ReflectionClass)) {
            return $item;
        }
		
        return $this->getInstance($item);
    }

    public function has(string $id): bool
    {
        try {
            $item = $this->resolve($id);
        } catch (NotFoundException $e) {
            return false;
        }
		
        if ($item instanceof ReflectionClass) {
            return $item->isInstantiable();
        }
		
        return isset($item);
    }

    public function set(string $key, $value)
    {
        $this->services[$key] = $value;
        return $this;
    }

    private function resolve($id)
    {
		
        try {
            $name = $id;
            if (isset($this->services[$id])) {
                $name = $this->services[$id];
                if (is_callable($name)) {
                    return $name();
                }
            }
            return (new ReflectionClass($name));
        } catch (ReflectionException $e) {
            throw new NotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function getInstance(ReflectionClass $item) {
        $constructor = $item->getConstructor();
        if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0) {
            return $item->newInstance();
        }
        $params = [];
        foreach ($constructor->getParameters() as $param) {
            if ($type = $param->getType()) {
                $params[] = $this->get($type->getName());
            }
        }
        return $item->newInstanceArgs($params);
    }
}
