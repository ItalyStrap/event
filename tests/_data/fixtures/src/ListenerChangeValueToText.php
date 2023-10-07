<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

class ListenerChangeValueToText
{
    public function changeText(object $event): void
    {
        $event->value = get_text();
    }
}
