<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

interface StateInterface
{
    public const BEFORE = 'before';
    public const AFTER = 'after';
    public const DURING = 'during';
}
