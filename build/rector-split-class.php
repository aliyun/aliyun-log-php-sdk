<?php

declare(strict_types=1);

use Aliyun\Log\Rectors\RemoveRequireOnceRector;
use Rector\Core\Configuration\Option;
use Rector\PSR4\Rector\Namespace_\MultipleClassFileToPsr4ClassesRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RemoveRequireOnceRector::class);
    $services->set(MultipleClassFileToPsr4ClassesRector::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/../Aliyun',
        __DIR__ . '/../sample',
    ]);
};
