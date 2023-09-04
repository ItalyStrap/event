<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

class EventForRenderer
{
    public string $rendered = 'Hello World';

    public function render(): string
    {
        return $this->rendered;
    }
}
