<?php

declare(strict_types=1);

namespace Kernel\Route;

use Kernel\Container\Container;
use Kernel\Http\Response;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

final class Route
{
    private array $routes = [];

    public function __construct(private Container $container)
    {
    }

    /** @param array{class-string, string} $action */
    public function get(string $path, array $action): self
    {
        $this->routes[] = [
            'method' => 'GET',
            'path'   => $path,
            'action' => $action,
        ];

        return $this;
    }

    public function dispatch(string $method, string $path): ?Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $parameters = $this->match($route['path'], $path);

            if ($parameters === null) {
                continue;
            }

            return $this->invoke($route['action'], $parameters);
        }

        return null;
    }

    /**
     * @throws \ReflectionException
     */
    private function invoke(array $action, array $routeParameters): Response
    {
        $controller = $this->container->get($action[0]);
        $method = new ReflectionMethod($controller, $action[1]);

        if (!$method->isPublic()) {
            throw new RuntimeException("Controller method {$action[1]} is not accessible.");
        }

        $arguments = [];

        foreach ($method->getParameters() as $parameter) {
            if (isset($routeParameters[$parameter->getName()])) {
                $arguments[] = $routeParameters[$parameter->getName()];

                continue;
            }

            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $arguments[] = $this->container->get($type->getName());

                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();

                continue;
            }

            throw new RuntimeException(
                "Unable to resolve route parameter {$parameter->getName()}.",
            );
        }

        $response = $method->invokeArgs($controller, $arguments);

        if (!$response instanceof Response) {
            throw new RuntimeException(
                "Controller method {$action[1]} must return an HTTP response.",
            );
        }

        return $response;
    }

    /** @return array<string, string>|null */
    private function match(string $routePath, string $requestPath): ?array
    {
        if ($routePath === '/' || $requestPath === '/') {
            return $routePath === $requestPath ? [] : null;
        }

        $routeSegments = explode('/', trim($routePath, '/'));
        $requestSegments = explode('/', trim($requestPath, '/'));

        if (count($routeSegments) !== count($requestSegments)) {
            return null;
        }

        $parameters = [];

        foreach ($routeSegments as $index => $segment) {
            $requestSegment = $requestSegments[$index];

            if (preg_match('/^\{([a-zA-Z][a-zA-Z0-9_]*)\}$/', $segment, $matches) === 1) {
                if (preg_match('/^[a-z0-9-]+$/', $requestSegment) !== 1) {
                    return null;
                }

                $parameters[$matches[1]] = $requestSegment;

                continue;
            }

            if ($segment !== $requestSegment) {
                return null;
            }
        }

        return $parameters;
    }
}
