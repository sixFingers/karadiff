<?php

namespace Karadiff;

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Env can be one in development|test|production
 */
$env = isset($_ENV['env']) ? $_ENV['env']: 'development';

if ($env === 'development') {
    error_reporting(E_ALL);
}

/**
 * Set a default time zone if not specified in .ini
 */
if ('' === ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Paris');
}

/**
 * Register a global error handler
 */
$whoops = new \Whoops\Run;

if ($env !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
        echo 'Friendly error page and send an email to the developer';
    });
}

$whoops->register();

/**
 * Instantiate dependency injector
 */
$injector = include('dependencies.php');

/**
 * Create and populate the request object
 */
// $request = Request::createFromGlobals();
$request = $injector->make('Symfony\Component\HttpFoundation\Request');

/**
 * Create and prepare a sample response
 */
// $response = new Response();
$response = $injector->make('Symfony\Component\HttpFoundation\Response');

/**
 * Initialize route dispatcher
 *
 * Routes get included from /src/routes.php.
 * Here we give a bit of sugar without worrying
 * too much about route specification format.
 * We assume its "METHOD path class@method"
 */
$dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
    $routes = include('routes.php');

    foreach ($routes as $route) {
        // Explode into [method, path, class@method]
        $routeInfo = explode(' ', $route);

        $r->addRoute($routeInfo[0], $routeInfo[1], $routeInfo[2]);
    }
});

/**
 * Actually dispatch the request
 */
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

/**
 * Handle common http cases.
 *
 * For 40x responses, we don't even bother
 * calling a controller action. We'll return
 * some static content.
 */
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page not found');
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->send();
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        $response->send();
        break;
    case \FastRoute\Dispatcher::FOUND:
        // Explode into [method, path]
        $handler = explode('@', $routeInfo[1]);
        // Let us specify controller actions without repeating root namespace
        $className = __NAMESPACE__ . '\\Controllers\\' . $handler[0];
        $method = $handler[1];
        $vars = $routeInfo[2];

        $class = $injector->make($className);
        $class->$method($vars);
        break;
}
