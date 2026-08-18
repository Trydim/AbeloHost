<?php

declare(strict_types=1);

namespace Kernel\Controller;

use Kernel\Actions\BlogActions;
use Kernel\Http\Request;
use Kernel\View\View;

final class BlogController
{
    private const POSTS_PER_PAGE = 3;
    private const SIMILAR_POSTS_LIMIT = 3;
    private const PAGINATION_RADIUS = 2;
    private const SORT_LATEST = 'latest';
    private const SORT_POPULAR = 'popular';

    public function __construct(
        private readonly BlogActions $blog,
        private readonly View $view,
        private readonly Request $request,
        private readonly ErrorController $errors,
    ) {
    }

    public function home(): void
    {
        $categories = $this->groupHomeRows(
            $this->blog->latestPostRowsByCategory(self::POSTS_PER_PAGE),
        );

        $this->view->render('pages/home.tpl', [
            'page_title' => 'Blog — статьи о разработке и продукте',
            'categories' => $categories,
        ]);
    }

    public function category(string $slug): void
    {
        $category = $this->blog->categoryBySlug($slug);

        if ($category === null) {
            $this->errors->pageNotFound();

            return;
        }

        $sort = $this->resolveSort();
        $totalPages = $this->calculateTotalPages((int) $category['post_count']);
        $page = $this->resolvePage($totalPages);

        $posts = $this->blog->postsForCategory(
            (int) $category['id'],
            $sort,
            $page,
            self::POSTS_PER_PAGE,
        );

        $this->view->render('pages/category.tpl', [
            'page_title' => $category['name'] . ' — Blog',
            'category' => $category,
            'posts' => $posts,
            'sort' => $sort,
            'pagination' => $this->buildPagination($page, $totalPages),
        ]);
    }

    public function post(string $slug): void
    {
        $post = $this->blog->postBySlug($slug);

        if ($post === null) {
            $this->errors->pageNotFound();

            return;
        }

        $postId = (int) $post['id'];

        $this->blog->incrementViews($postId);

        $post['id'] = $postId;
        $post['views'] = (int) $post['views'] + 1;
        $post['categories'] = $this->blog->categoriesForPost($postId);

        $this->view->render('pages/post.tpl', [
            'page_title' => $post['title'] . ' — AbeloHost Blog',
            'post' => $post,
            'similar_posts' => $this->blog->similarPosts($postId, self::SIMILAR_POSTS_LIMIT),
        ]);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private function groupHomeRows(array $rows): array
    {
        $categories = [];

        foreach ($rows as $row) {
            $categoryId = (int) $row['category_id'];

            if (!isset($categories[$categoryId])) {
                $categories[$categoryId] = [
                    'id' => $categoryId,
                    'name' => $row['category_name'],
                    'slug' => $row['category_slug'],
                    'description' => $row['category_description'],
                    'posts' => [],
                ];
            }

            $categories[$categoryId]['posts'][] = [
                'id' => (int) $row['post_id'],
                'title' => $row['post_title'],
                'slug' => $row['post_slug'],
                'image' => $row['post_image'],
                'description' => $row['post_description'],
                'views' => (int) $row['post_views'],
                'published_at' => $row['post_published_at'],
            ];
        }

        return array_values($categories);
    }

    private function resolveSort(): string
    {
        return $this->request->getString('sort', self::SORT_LATEST) === self::SORT_POPULAR
            ? self::SORT_POPULAR
            : self::SORT_LATEST;
    }

    private function resolvePage(int $totalPages): int
    {
        $page = filter_var(
            $this->request->getString('page', '1'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]],
        );

        return min($page === false ? 1 : $page, $totalPages);
    }

    private function calculateTotalPages(int $totalPosts): int
    {
        return max(1, (int) ceil($totalPosts / self::POSTS_PER_PAGE));
    }

    /**
     * @return array{
     *     current_page: int,
     *     total_pages: int,
     *     has_previous: bool,
     *     has_next: bool,
     *     pages: list<int>
     * }
     */
    private function buildPagination(int $page, int $totalPages): array
    {
        $firstPage = max(1, $page - self::PAGINATION_RADIUS);
        $lastPage = min($totalPages, $page + self::PAGINATION_RADIUS);

        return [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
            'pages' => range($firstPage, $lastPage),
        ];
    }
}
