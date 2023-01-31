<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    // get parameters
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    //$containerConfigurator->import(SetList::DEAD_CODE);
//    $containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::SYMFONY_50);
    //$containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::SYMFONY_50_TYPES);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();
    //$services = $containerConfigurator->services();
    //$services->set(TypedPropertyRector::class);

    $rectorConfig->sets([
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_70,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_80,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_90,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_91,
    ]);

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
