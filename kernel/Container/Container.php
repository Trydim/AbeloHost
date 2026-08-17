<?php

declare(strict_types=1);

namespace Kernel\Container;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    private array $factories = [];

    private array $instances = [];

    public function singleton(string $id, callable $factory): void {
        $this->factories[$id] = $factory;
    }

    /**
     * @throws \ReflectionException
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->factories[$id])) {
            return $this->buildFactory($id);
        }

        return $this->autowire($id);
    }

    private function buildFactory(string $id): object
    {
        $service = ($this->factories[$id])($this);

        if (!is_object($service)) {
            throw new RuntimeException("Factory {$id} must return an object.");
        }

        return $this->instances[$id] = $service;
    }

    /**
     * @throws \ReflectionException
     */
    private function autowire(string $id): object
    {
        if (!class_exists($id)) {
            throw new RuntimeException("Service {$id} is not registered.");
        }

        $reflection = new ReflectionClass($id);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Service {$id} cannot be instantiated.");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        return $reflection->newInstanceArgs(
            $this->resolveConstructor($id, $constructor),
        );
    }

    private function resolveConstructor(string $id, ReflectionMethod $constructor): array
    {
        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $arguments[] = $this->get($type->getName());

                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();

                continue;
            }

            throw new RuntimeException(
                "Unable to resolve dependency {$parameter->getName()} in {$id}.",
            );
        }

        return $arguments;
    }
}
