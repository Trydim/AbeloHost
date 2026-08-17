<?php

declare(strict_types=1);

use Kernel\DB\Database;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$migrationFiles = glob(__DIR__ . '/*.sql');

if ($migrationFiles === false || $migrationFiles === []) {
  throw new RuntimeException('Не найдены SQL-миграции в database/migrations.');
}

$pdo = Database::connect();

foreach ($migrationFiles as $migrationFile) {
  $schema = file_get_contents($migrationFile);

  if ($schema === false) {
    throw new RuntimeException("Не удалось прочитать миграцию {$migrationFile}.");
  }

  foreach (preg_split('/;\s*(?:\R|$)/', $schema) as $statement) {
    $statement = trim($statement);

    if ($statement !== '') {
      $pdo->exec($statement);
    }
  }

  echo 'Применена миграция: ' . basename($migrationFile) . PHP_EOL;
}
