<?php

declare(strict_types=1);

use Kernel\Controller\BlogController;
use Kernel\Route\Route;

return static function (Route $router): void {
    $router
        ->get('/', [BlogController::class, 'home'])
        ->get('/category/{slug}', [BlogController::class, 'category'])
        ->get('/post/{slug}', [BlogController::class, 'post']);
};
