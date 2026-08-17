<?php

declare(strict_types=1);

namespace Kernel\Controller;

use Kernel\View\View;

class BlogController
{
    public function __construct(private View $view)
    {
    }

    public function home(): void
    {
        $this->view->render("pages/home.tpl");
    }
}
