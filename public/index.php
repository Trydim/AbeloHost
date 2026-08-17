<?php

declare(strict_types=1);

use Kernel\Kernel;

phpinfo();
die();

require dirname(__DIR__) . '/vendor/autoload.php';

Kernel::getInstance()->create();
