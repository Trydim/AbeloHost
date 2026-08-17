<?php

declare(strict_types=1);

namespace Kernel\Controller;

use Kernel\Actions\BlogActions;
use Kernel\View\View;

class BlogController
{
    public function __construct(
        private BlogActions $blog,
        private View $view,
    ) {
    }

    public function home(): void
    {
        $categories = $this->blog->categories();

        foreach ($categories as &$category) {
            $category['posts'] = $this->blog->postsForCategory((int) $category['id'], 3);
        }

        $this->view->render('pages/home.tpl', [
            'page_title' => 'Blog — статьи о разработке и продукте',
            'categories' => $categories,
        ]);
    }
}
