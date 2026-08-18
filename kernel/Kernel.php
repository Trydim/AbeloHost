<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Container\Container;
use Kernel\Controller\ErrorController;
use Kernel\Http\Exception\NotFoundHttpException;
use Kernel\Http\Request;
use Kernel\Http\Response;
use Kernel\Route\Route;
use RuntimeException;
use Throwable;

final class Kernel
{
    public string $basePath;

    private static ?self $instance = null;

    private Container $container;

    private Route $router;

    private bool $configured = false;

    private function __construct(string $basePath)
    {
        $this->basePath  = $basePath;
        $this->container = new Container();
        $this->router    = new Route($this->container);

        $this->container->singleton(
            self::class,
            fn (Container $_): self => $this
        );
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
            $request  = $this->get(Request::class);
            $response = $this->router->dispatch($request->method(), $request->path());

            if ($response === null) {
                $controller = $this->get(ErrorController::class);
                $response   = $controller->pageNotFound();
            }
        } catch (NotFoundHttpException) {
            $controller = $this->get(ErrorController::class);
            $response   = $controller->pageNotFound();
        } catch (Throwable $exception) {
            error_log((string) $exception);

            try {
                $controller = $this->get(ErrorController::class);
                $response   = $controller->pageServerError();
            } catch (Throwable $renderException) {
                error_log((string) $renderException);

                $response = Response::text('Internal Server Error', 500);
            }
        }

        $response->send();
    }

    public function configure(callable $configurator): self
    {
        if ($this->configured) {
            throw new LogicException('The kernel is already configured.');
        }

        $configurator($this->container);
        $this->loadRoutes();
        $this->configured = true;

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
