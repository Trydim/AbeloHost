<?php

declare(strict_types=1);

use Kernel\Kernel;

if (!function_exists('app')) {
    function app(): Kernel
    {
        return Kernel::getInstance();
    }
}
