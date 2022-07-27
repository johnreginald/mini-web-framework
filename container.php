<?php

use App\Kernel;
use Infrastructure\Database;
use Infrastructure\Auth;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Loader\YamlFileLoader;

$loader = new YamlFileLoader(new FileLocator(__DIR__));
$routes = $loader->load(__DIR__ . '/config/routes.yaml');

$containerBuilder = new DependencyInjection\ContainerBuilder();

$containerBuilder->register('context', Routing\RequestContext::class);
$containerBuilder->register('matcher', Routing\Matcher\UrlMatcher::class)
    ->setArguments([$routes, new Reference('context')]);

$containerBuilder->register('request_stack', HttpFoundation\RequestStack::class);
$containerBuilder->register('controller_resolver', HttpKernel\Controller\ControllerResolver::class);
$containerBuilder->register('container_controller_resolver', HttpKernel\Controller\ContainerControllerResolver::class)
    ->setArguments([new Reference('service_container')]);
$containerBuilder->register('argument_resolver', HttpKernel\Controller\ArgumentResolver::class);

$containerBuilder->register('listener.router', HttpKernel\EventListener\RouterListener::class)
    ->setArguments([new Reference('matcher'), new Reference('request_stack')]);

$containerBuilder->register('listener.response', HttpKernel\EventListener\ResponseListener::class)
    ->setArguments(['UTF-8']);

$containerBuilder->register('dispatcher', EventDispatcher\EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')]);

$containerBuilder->register('framework', Kernel::class)
    ->setArguments([
        new Reference('dispatcher'),
        new Reference('container_controller_resolver'),
        new Reference('request_stack'),
        new Reference('argument_resolver'),
    ])
    ->setPublic(true);

$containerBuilder->register('database', Database::class);

return $containerBuilder;