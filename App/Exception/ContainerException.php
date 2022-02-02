<?php

declare(strict_types = 1);

namespace App\Exception;

use Psr\Container\ContainerExceptionInterface;
use Exception;

class ContainerException extends Exception implements ContainerExceptionInterface {}
