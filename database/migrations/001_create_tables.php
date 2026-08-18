<?php

declare(strict_types=1);

return static function (PDO $pdo): void {
    $statement = $pdo->query(
        "SELECT table_name
         FROM information_schema.tables
         WHERE table_schema = DATABASE()
           AND table_name IN ('categories', 'posts', 'post_categories')",
    );
    $existingTables = $statement->fetchAll(PDO::FETCH_COLUMN);

    // Поддержка схемы, созданной прежней SQL-миграцией до появления учёта версий.
    if (count($existingTables) === 3) {
        return;
    }

    if ($existingTables !== []) {
        throw new RuntimeException(
            'Обнаружена частично созданная схема: ' . implode(', ', $existingTables) .
            '. Восстановите схему перед повторным запуском миграций.',
        );
    }

    $pdo->exec(
        'CREATE TABLE categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(160) NOT NULL,
            description TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL,

            UNIQUE KEY categories_name_unique (name),
            UNIQUE KEY categories_slug_unique (slug)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    );

    $pdo->exec(
        'CREATE TABLE posts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(270) NOT NULL,
            image VARCHAR(255) NULL,
            description TEXT NOT NULL,
            content LONGTEXT NOT NULL,
            views INT UNSIGNED NOT NULL DEFAULT 0,
            published_at DATETIME NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL,

            UNIQUE KEY posts_slug_unique (slug),
            KEY posts_published_at_index (published_at),
            KEY posts_views_index (views)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    );

    $pdo->exec(
        'CREATE TABLE post_categories (
            post_id INT UNSIGNED NOT NULL,
            category_id INT UNSIGNED NOT NULL,

            PRIMARY KEY (post_id, category_id),
            KEY post_categories_category_id_index (category_id),
            CONSTRAINT post_categories_post_id_foreign
                FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
            CONSTRAINT post_categories_category_id_foreign
                FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    );
};
