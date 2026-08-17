<?php

declare(strict_types=1);

namespace Kernel\Controller;

class ErrorController
{
    /** @noinspection PhpUnused */
    public function pageNotFound(): void
    {
        http_response_code(404);
        echo 'error 404';
    }

    /** @noinspection PhpUnused */
    public function pageServerError(): void
    {
        http_response_code(404);
        echo 'error 5004';
    }
}
