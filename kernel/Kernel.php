<?php

declare(strict_types=1);

namespace Kernel;

final class Kernel
{
    function __construct()
    {

    }

    public function create(): void
    {
      $pdo = \Kernel\DB\Database::connect();

      $result = $pdo->query('SELECT * FROM `categories`');
      var_dump($result->fetchAll());
      $result = $pdo->query('SELECT * FROM `posts`');
      var_dump($result->fetchAll());
    }
}
