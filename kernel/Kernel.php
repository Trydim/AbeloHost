<?php

declare(strict_types=1);

namespace Kernel;


use Kernel\Container\Container;
use Kernel\Controller\ErrorController;
use Kernel\DB\Database;
use Kernel\Http\Request;
use Kernel\Route\Route;
use PDO;
use RuntimeException;
use Throwable;

final class Kernel
{
    private static ?self $instance = null;

    private Container $container;

    private Route $router;

    public string $basePath;

    private function __construct(string $basePath)
    {
        $this->basePath  = $basePath;
        $this->container = new Container();
        $this->container->singleton(
            self::class,
            fn (Container $_): self => $this
        );

        $this->router = new Route($this->container);

        $this->configure();
        $this->loadRoutes();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self(dirname(__DIR__));
    }

    /**
     * @throws \ReflectionException
     */
    public function get(string $id): object
    {
        return $this->container->get($id);
    }

    public function create(): void
    {
        try {
            $request = $this->get(Request::class);

            $result = $this->router->dispatch($request->method(), $request->path());

            if (!$result) {
                $controller = $this->get(ErrorController::class);
                $controller->pageNotFound();
            }
        } catch (Throwable $exception) {
            error_log((string) $exception);

            $controller = $this->get(ErrorController::class);
            $controller->pageServerError();
        }
    }

    private function configure(): self
    {
        $this->container->singleton(
            PDO::class,
            static fn (): PDO => Database::connect(),
        );
        $this->container->singleton(
            Request::class,
            static fn (): Request => Request::fromGlobals(),
        );

        return $this;
    }

    private function loadRoutes(): void
    {
        $routes = require $this->basePath . '/routes/web.php';

        if (!is_callable($routes)) {
            throw new RuntimeException('The routes/web.php file must return a callable.');
        }

        $routes($this->router);
    }
}
