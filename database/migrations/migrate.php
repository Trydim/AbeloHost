<?php

declare(strict_types=1);

use Kernel\DB\Database;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

const MIGRATION_FILE_PATTERN = '/^(\d{3,})_[a-z0-9_]+\.php$/';
const MIGRATION_TABLE = 'schema_migrations';

/**
 * @return array<string, string>
 */
function discoverMigrations(string $directory): array
{
    $files = glob($directory . '/*.php');

    if ($files === false) {
        throw new RuntimeException("Не удалось прочитать директорию миграций {$directory}.");
    }

    $migrations = [];
    $versions = [];

    foreach ($files as $file) {
        $filename = basename($file);

        if (preg_match(MIGRATION_FILE_PATTERN, $filename, $matches) !== 1) {
            continue;
        }

        $version = $matches[1];

        if (isset($versions[$version])) {
            throw new RuntimeException("Обнаружены миграции с одинаковой версией {$version}.");
        }

        $versions[$version] = true;
        $migrations[pathinfo($filename, PATHINFO_FILENAME)] = $file;
    }

    if ($migrations === []) {
        throw new RuntimeException('Не найдены PHP-миграции в database/migrations.');
    }

    ksort($migrations, SORT_NATURAL);

    return $migrations;
}

function tableExists(PDO $pdo, string $table): bool
{
    $statement = $pdo->prepare(
        'SELECT EXISTS (
             SELECT 1
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
               AND table_name = :table
         )',
    );
    $statement->execute(['table' => $table]);

    return (bool) $statement->fetchColumn();
}

function acquireMigrationLock(PDO $pdo): string
{
    $database = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
    $lockName = 'abelohost_migrations_' . substr(hash('sha256', $database), 0, 32);
    $statement = $pdo->prepare('SELECT GET_LOCK(:lockName, 30)');
    $statement->execute(['lockName' => $lockName]);

    if ((int) $statement->fetchColumn() !== 1) {
        throw new RuntimeException('Не удалось получить блокировку для запуска миграций.');
    }

    return $lockName;
}

function releaseMigrationLock(PDO $pdo, string $lockName): void
{
    $statement = $pdo->prepare('SELECT RELEASE_LOCK(:lockName)');
    $statement->execute(['lockName' => $lockName]);
}

$migrations = discoverMigrations(__DIR__);
$pdo = Database::connect();
$lockName = acquireMigrationLock($pdo);

try {
    if (!tableExists($pdo, MIGRATION_TABLE)) {
        $pdo->exec(
            'CREATE TABLE schema_migrations (
                migration VARCHAR(255) NOT NULL PRIMARY KEY,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    $appliedMigrations = $pdo
        ->query('SELECT migration FROM schema_migrations ORDER BY migration')
        ->fetchAll(PDO::FETCH_COLUMN);
    $appliedMigrations = array_fill_keys($appliedMigrations, true);
    $recordMigration = $pdo->prepare(
        'INSERT INTO schema_migrations (migration) VALUES (:migration)',
    );
    $appliedCount = 0;

    foreach ($migrations as $name => $file) {
        if (isset($appliedMigrations[$name])) {
            continue;
        }

        $migration = require $file;

        if (!is_callable($migration)) {
            throw new RuntimeException("Миграция {$name} должна возвращать callable.");
        }

        try {
            $migration($pdo);
            $recordMigration->execute(['migration' => $name]);
        } catch (Throwable $exception) {
            throw new RuntimeException("Ошибка применения миграции {$name}.", 0, $exception);
        }

        $appliedCount++;
        echo "Применена миграция: {$name}" . PHP_EOL;
    }

    if ($appliedCount === 0) {
        echo 'Новых миграций нет.' . PHP_EOL;
    }
} finally {
    releaseMigrationLock($pdo, $lockName);
}
