<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Container\Container;
use Kernel\DB\Database;
use PDO;

final class Kernel
{
    private static ?self $instance = null;

    private Container $container;

    private function __construct()
    {
        $this->container = new Container();
        $this->configure();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function create(): void
    {
        $pdo = $this->container->get(PDO::class);

        $result = $pdo->query('SELECT * FROM `categories`');
        var_dump($result->fetchAll());
        $result = $pdo->query('SELECT * FROM `posts`');
        var_dump($result->fetchAll());
    }

    private function configure(): self
    {
        $this->container->set(
            PDO::class,
            function (Container $container) {
                return Database::connect();
            }
        );

        return $this;
    }
}
