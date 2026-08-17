<?php

declare(strict_types=1);

use Kernel\Kernel;

require dirname(__DIR__) . '/vendor/autoload.php';

Kernel::getInstance()->create();
