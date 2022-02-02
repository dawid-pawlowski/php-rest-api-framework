<?php

declare(strict_types = 1);

namespace App\Exception;

use Psr\Container\NotFoundExceptionInterface;
use Exception;

class NotFoundException extends Exception implements NotFoundExceptionInterface {}
