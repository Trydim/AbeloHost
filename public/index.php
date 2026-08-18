<?php

declare(strict_types=1);

use Kernel\Container\Container;
use Kernel\DB\Database;
use Kernel\Http\Request;
use Kernel\Kernel;

require dirname(__DIR__) . '/vendor/autoload.php';

Kernel::getInstance()
    ->configure(static function (Container $container): void {
        $container->singleton(
            PDO::class,
            static fn (Container $_): PDO => Database::connect(),
        );
        $container->singleton(
            Request::class,
            static fn (Container $_): Request => Request::fromGlobals(),
        );
    })->create();
