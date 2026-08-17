<?php

declare(strict_types=1);

final class Kernel
{
    function __construct()
    {

    }

    public function create(): void
    {
      echo 'hello world';
    }
}
