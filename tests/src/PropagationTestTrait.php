<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

trait PropagationTestTrait
{
    public function testIsPropagationStopped(): void
    {
        $instance = $this->makeInstance();
        $this->assertFalse($instance->isPropagationStopped());
    }

    public function testStopPropagation(): void
    {
        $instance = $this->makeInstance();
        $instance->stopPropagation();
        $this->assertTrue($instance->isPropagationStopped());
    }
}
