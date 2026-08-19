<?php

declare(strict_types=1);

namespace Kernel\Actions;

use InvalidArgumentException;
use PDO;

final class BlogActions
{
    private const CATEGORY_ORDER_BY = [
        'latest' => 'p.published_at DESC, p.id DESC',
        'popular' => 'p.views DESC, p.published_at DESC, p.id DESC',
    ];

    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function latestPostRowsByCategory(int $limit): array
    {
        self::assertPositive($limit, 'limit');

        $statement = $this->pdo->prepare(
            'WITH ranked_posts AS (
                 SELECT pc.category_id,
                        p.id,
                        p.title,
                        p.slug,
                        p.image,
                        p.description,
                        p.views,
                        p.published_at,
                        ROW_NUMBER() OVER (
                            PARTITION BY pc.category_id
                            ORDER BY p.published_at DESC, p.id DESC
                        ) AS post_position
                 FROM posts p
                 JOIN post_categories pc ON pc.post_id = p.id
                 WHERE p.deleted_at IS NULL
             )
             SELECT c.id AS category_id,
                    c.name AS category_name,
                    c.slug AS category_slug,
                    c.description AS category_description,
                    rp.id AS post_id,
                    rp.title AS post_title,
                    rp.slug AS post_slug,
                    rp.image AS post_image,
                    rp.description AS post_description,
                    rp.views AS post_views,
                    rp.published_at AS post_published_at
             FROM categories c
             JOIN ranked_posts rp ON rp.category_id = c.id AND rp.post_position <= :limit
             WHERE c.deleted_at IS NULL
             ORDER BY c.name, rp.published_at DESC, rp.id DESC',
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function categoryBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT c.id,
                    c.name,
                    c.slug,
                    c.description,
                    (
                        SELECT COUNT(*)
                        FROM post_categories pc
                        JOIN posts p ON p.id = pc.post_id AND p.deleted_at IS NULL
                        WHERE pc.category_id = c.id
                    ) AS post_count
             FROM categories c
             WHERE c.slug = :slug AND c.deleted_at IS NULL
             LIMIT 1',
        );
        $statement->execute(['slug' => $slug]);
        $category = $statement->fetch();

        return $category === false ? null : $category;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function postsForCategory(int $categoryId, string $sort, int $page, int $perPage): array
    {
        self::assertPositive($categoryId, 'categoryId');
        self::assertPositive($page, 'page');
        self::assertPositive($perPage, 'perPage');

        $orderBy = self::CATEGORY_ORDER_BY[$sort]
            ?? throw new InvalidArgumentException("Unsupported category sort: {$sort}.");
        $offset = ($page - 1) * $perPage;

        $statement = $this->pdo->prepare(
            "SELECT p.id,
                    p.title,
                    p.slug,
                    p.image,
                    p.description,
                    p.views,
                    p.published_at
             FROM posts p
             JOIN post_categories pc ON pc.post_id = p.id
             WHERE pc.category_id = :categoryId AND p.deleted_at IS NULL
             ORDER BY {$orderBy}
             LIMIT :limit OFFSET :offset",
        );
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function postBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id,
                    title,
                    slug,
                    image,
                    description,
                    content,
                    views,
                    published_at
             FROM posts
             WHERE slug = :slug AND deleted_at IS NULL
             LIMIT 1',
        );
        $statement->execute(['slug' => $slug]);
        $post = $statement->fetch();

        return $post === false ? null : $post;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function categoriesForPost(int $postId): array
    {
        self::assertPositive($postId, 'postId');

        $statement = $this->pdo->prepare(
            'SELECT c.id, c.name, c.slug
             FROM categories c
             JOIN post_categories pc ON pc.category_id = c.id
             WHERE pc.post_id = :postId AND c.deleted_at IS NULL
             ORDER BY c.name',
        );
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function incrementViews(int $postId): void
    {
        self::assertPositive($postId, 'postId');

        $statement = $this->pdo->prepare(
            'UPDATE posts
             SET views = views + 1
             WHERE id = :postId AND deleted_at IS NULL',
        );
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function similarPosts(int $postId, int $limit): array
    {
        self::assertPositive($postId, 'postId');
        self::assertPositive($limit, 'limit');

        $statement = $this->pdo->prepare(
            'SELECT p.id,
                    p.title,
                    p.slug,
                    p.image,
                    p.description,
                    p.views,
                    p.published_at,
                    COUNT(*) AS shared_categories
             FROM posts p
             JOIN post_categories pc ON pc.post_id = p.id
             JOIN categories c ON c.id = pc.category_id AND c.deleted_at IS NULL
             JOIN post_categories current_pc
                 ON current_pc.category_id = pc.category_id AND current_pc.post_id = :sourcePostId
             WHERE p.id != :postId AND p.deleted_at IS NULL
             GROUP BY p.id
             ORDER BY shared_categories DESC, p.published_at DESC, p.id DESC
             LIMIT :limit',
        );
        $statement->bindValue(':sourcePostId', $postId, PDO::PARAM_INT);
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    private static function assertPositive(int $value, string $parameter): void
    {
        if ($value < 1) {
            throw new InvalidArgumentException("{$parameter} must be greater than zero.");
        }
    }
}
