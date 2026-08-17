<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Container\Container;
use Kernel\DB\Database;
use Kernel\Route\Route;
use PDO;
use RuntimeException;
use Throwable;

final class Kernel
{
    private static ?self $instance = null;

    private Container $container;

    private Route $router;

    private function __construct()
    {
        $this->container = new Container();
        $this->router    = new Route($this->container);

        $this->configure();
        $this->loadRoutes();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function create(): void
    {
        try {
            $this->router->dispatch('GET', '/');
        } catch (Throwable $exception) {
            error_log((string) $exception);

            http_response_code(500);
            echo 'Server error.';
        }
    }

    private function configure(): self
    {
        $this->container->singleton(
            PDO::class,
            function () {
                return Database::connect();
            }
        );

        return $this;
    }

    private function loadRoutes(): void
    {
        $routes = require dirname(__DIR__) . '/routes/web.php';

        if (!is_callable($routes)) {
            throw new RuntimeException('The routes/web.php file must return a callable.');
        }

        $routes($this->router);
    }
}
