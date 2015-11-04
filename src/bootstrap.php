<?php

namespace Karadiff;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Env can be one in development|test|production
 */
$env = isset($_ENV['env']) ? $_ENV['env']: 'development';

if ($env === 'development') {
    error_reporting(E_ALL);
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

throw new \Exception;
