<?php

declare(strict_types=1);

namespace Kernel\Actions;

use PDO;

final class BlogActions
{
    public function __construct(private PDO $pdo)
    {
    }

    public function categories(): array
    {
        $statement = $this->pdo->query('SELECT * FROM categories');

        return $statement->fetchAll();
    }


    public function postsForCategory(int $categoryId, int $limit): array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
            FROM posts p
            JOIN post_categories pc ON pc.post_id = p.id
            WHERE pc.category_id = :categoryId AND p.deleted_at IS NULL
            ORDER BY p.published_at DESC, p.id DESC
            LIMIT :limit',
        );
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
