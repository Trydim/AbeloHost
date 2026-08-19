<?php

declare(strict_types=1);

namespace Kernel\Service;

use Kernel\Actions\BlogActions;

final class PostView
{
    private const VIEWED_POSTS_SESSION_KEY = 'viewed_posts';

    public function __construct(private readonly BlogActions $blog)
    {
    }

    public function trackOncePerSession(int $postId, int $currentViews): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['cookie_httponly' => true]);
        }

        $viewedPosts = $_SESSION[self::VIEWED_POSTS_SESSION_KEY] ?? [];

        if (isset($viewedPosts[$postId])) {
            return $currentViews;
        }

        $this->blog->incrementViews($postId);

        $viewedPosts[$postId] = time();
        $_SESSION[self::VIEWED_POSTS_SESSION_KEY] = $viewedPosts;

        return $currentViews + 1;
    }
}
