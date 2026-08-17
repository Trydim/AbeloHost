<?php

declare(strict_types=1);

namespace Kernel\DB;

use PDO;

final class Database
{
  public static function connect(): PDO
  {
    $host     = self::environment('DB_HOST', 'db');
    $port     = self::environment('DB_PORT', '3306');
    $database = self::environment('DB_DATABASE', 'abelohost');
    $username = self::environment('DB_USERNAME', 'abelohost');
    $password = self::environment('DB_PASSWORD', 'secret');

    return new PDO(
      "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
      $username,
      $password,
      [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
      ],
    );
  }

  private static function environment(string $name, string $default): string
  {
    $value = getenv($name);

    return $value === false || $value === '' ? $default : $value;
  }
}
