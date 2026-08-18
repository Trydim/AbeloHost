<?php

declare(strict_types=1);

namespace Kernel\Http;

final class Request
{
    /**
     * @param array<string, mixed> $server
     * @param array<string, mixed> $query
     */
    private function __construct(
        private array $server,
        private array $query,
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self($_SERVER, $_GET); // $POST
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function path(): string
    {
        $path = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        return rtrim($path, '/') ?: '/';
    }

    public function getString(string $key, string $default = ''): string
    {
        $value = $this->query[$key] ?? $default;

        return is_string($value) ? $value : $default;
    }
}
