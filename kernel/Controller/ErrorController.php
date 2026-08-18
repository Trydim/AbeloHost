<?php

declare(strict_types=1);

namespace Kernel\Controller;

use Kernel\Http\Response;
use Kernel\View\View;

class ErrorController
{
    public function __construct(private View $view)
    {
    }

    /** @noinspection PhpUnused */
    public function pageNotFound(): Response
    {
        return Response::html($this->view->render('pages/404.tpl', [
            'page_title' => 'Страница не найдена — AbeloHost Blog',
        ]), 404);
    }

    /** @noinspection PhpUnused */
    public function pageServerError(): Response
    {
        return Response::html($this->view->render('pages/500.tpl', [
            'page_title' => 'Ошибка сервера — AbeloHost Blog',
        ]), 500);
    }
}
