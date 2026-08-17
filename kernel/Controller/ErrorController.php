<?php

declare(strict_types=1);

namespace Kernel\Controller;

use Kernel\View\View;

class ErrorController
{
    public function __construct(private View $view)
    {
    }

    /** @noinspection PhpUnused */
    public function pageNotFound(): void
    {
        http_response_code(404);
        $this->view->render("pages/404.tpl");
    }

    /** @noinspection PhpUnused */
    public function pageServerError(): void
    {
        http_response_code(500);
        $this->view->render("pages/500.tpl");
    }
}
