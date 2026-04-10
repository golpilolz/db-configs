<?php

declare(strict_types=1);


use Golpilolz\DBConfigs\Repository\SiteVariableRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $container = $configurator->services()
        ->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    /*** Repository ***/
    $container->load('Golpilolz\\DBConfigs\\Repository\\', '../../Repository/*');

    /*** Services ***/
    $container->load('Golpilolz\\DBConfigs\\Service\\', '../../Service/*');
};
