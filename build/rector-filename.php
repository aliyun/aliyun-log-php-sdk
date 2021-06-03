<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Restoration\Rector\ClassLike\UpdateFileNameByClassNameFileSystemRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(UpdateFileNameByClassNameFileSystemRector::class);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/../Aliyun',
        __DIR__ . '/../sample',
    ]);
};
