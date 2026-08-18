<?php

declare(strict_types=1);

namespace Kernel\Http;

use InvalidArgumentException;

final readonly class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $content = '',
        private int $status = 200,
        private array $headers = [],
    ) {
        if ($status < 100 || $status > 599) {
            throw new InvalidArgumentException('HTTP status code must be between 100 and 599.');
        }

        foreach ($headers as $name => $value) {
            if (preg_match('/^[A-Za-z0-9-]+$/D', $name) !== 1) {
                throw new InvalidArgumentException('Response header name is invalid.');
            }

            if (str_contains($value, "\r") || str_contains($value, "\n")) {
                throw new InvalidArgumentException("Response header {$name} is invalid.");
            }
        }
    }

    /**
     * @param array<string, string> $headers
     */
    public static function html(string $content, int $status = 200, array $headers = []): self
    {
        return new self(
            $content,
            $status,
            ['Content-Type' => 'text/html; charset=UTF-8', ...$headers],
        );
    }

    /**
     * @param array<string, string> $headers
     */
    public static function text(string $content, int $status = 200, array $headers = []): self
    {
        return new self(
            $content,
            $status,
            ['Content-Type' => 'text/plain; charset=UTF-8', ...$headers],
        );
    }

    public function content(): string
    {
        return $this->content;
    }

    public function status(): int
    {
        return $this->status;
    }

    /** @return array<string, string> */
    public function headers(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}", true);
        }

        echo $this->content;
    }
}
