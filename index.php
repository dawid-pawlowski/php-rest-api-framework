<?php declare(strict_types = 1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . '/vendor/autoload.php';

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

use App\Middleware\ErrorHandlingMiddleware;
use App\Middleware\RoutingMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GUIDv4Middleware;
use App\Middleware\CORSMiddleware;
use App\Middleware\JSONParsingMiddleware;
use App\Middleware\QueryStringParsingMiddleware;
use App\Middleware\SanitizingMiddleware;
use App\Middleware\ValidatingMiddleware;

use App\Core\Container;
use App\Core\Validator;
use App\Core\Router;
use App\Core\MiddlewareWrapper;
use App\Core\DefaultHandler;
use App\Core\Generator;

use App\Database\Database;

function exception_handler($e)
{
    error_log($e->getMessage());
    http_response_code(500);
    if (filter_var(ini_get('display_errors'), FILTER_VALIDATE_BOOLEAN)) {
        echo $e;
    } else {
        echo "<h1>500 Internal Server Error</h1>
              An internal server error has been occurred.<br>
              Please try again later.";
    }
}

set_exception_handler('exception_handler');

set_error_handler(function ($level, $message, $file = '', $line = 0)
{
    throw new ErrorException($message, 0, $level, $file, $line);
});

register_shutdown_function(function ()
{
    $error = error_get_last();
    if ($error !== null) {
        $e = new ErrorException(
            $error['message'], 0, $error['type'], $error['file'], $error['line']
        );
        exception_handler($e);
    }
});

$request = ServerRequestFactory::fromGlobals();
$emitter = new SapiEmitter();

$routes = [
    ['GET', '/test/test', 'App\Test\TestAction'],
];

$validatorRules = [
    'username' => 'length:50',
    'user_email' => 'email',
	'min' => 'integer',
	'max' => 'integer',
    'country_code' => 'ISOCountryCode',
	'image' => 'base64',
];

$container = new Container();
//$container->set('App\Database\Database', 				function() use ($database) { return $database; });
$container->set('Psr\Http\Message\ResponseInterface', 	Laminas\Diactoros\Response::class);


$stack = new MiddlewareWrapper(new RoutingMiddleware(new Router($container, $routes)), 	        new DefaultHandler(404));
$stack = new MiddlewareWrapper(new GUIDv4Middleware(new Generator()),                           $stack);
$stack = new MiddlewareWrapper(new ValidatingMiddleware(new Validator($validatorRules)),        $stack);
$stack = new MiddlewareWrapper(new SanitizingMiddleware(), 								        $stack);
$stack = new MiddlewareWrapper(new QueryStringParsingMiddleware(),								$stack);
//$stack = new MiddlewareWrapper(new JSONParsingMiddleware(),								        $stack);
$stack = new MiddlewareWrapper(new CORSMiddleware(), 									        $stack);
$stack = new MiddlewareWrapper(new ErrorHandlingMiddleware(), $stack);

$response = $stack->handle($request);
$emitter->emit($response);
