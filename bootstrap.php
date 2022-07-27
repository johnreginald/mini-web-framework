<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;

$container = require_once __DIR__ . '/container.php';

$request = Request::createFromGlobals();

$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load(__DIR__ . '/config/services.yaml');
$container->compile();

$response = $container->get('framework')->handle($request);

$response->send();