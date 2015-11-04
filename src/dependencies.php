<?php

$injector = new \Auryn\Injector;

$injector->alias('Response', 'Symfony\Component\HttpFoundation\Response');
$injector->share('Symfony\Component\HttpFoundation\Response');
$injector->alias('Request', 'Symfony\Component\HttpFoundation\Request');
$injector->share('Symfony\Component\HttpFoundation\Request');
$injector->define('Symfony\Component\HttpFoundation\Request', [
    ':query' => $_GET,
    ':request' => $_POST,
    ':attributes' => array(),
    ':cookies' => $_COOKIE,
    ':files' => $_FILES,
    ':server' => $_SERVER,
]);

return $injector;
