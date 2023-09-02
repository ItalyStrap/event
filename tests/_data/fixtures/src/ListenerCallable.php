<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

class ListenerCallable
{
    public function __invoke(int $arg1 = 0, int $arg2 = 0): int
    {
        return $arg1 + $arg2;
    }
}
