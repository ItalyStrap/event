<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit\PsrDispatcher;

use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\PsrDispatcher\CallableFactory;

class CallableFactoryTest extends UnitTestCase
{
    private function makeInstance(): CallableFactory
    {
        return new CallableFactory();
    }

    /**
     * @test
     */
    public function itShouldBuildCallable()
    {
        $sut = $this->makeInstance();
        $sut->buildCallable(function () {
        });
    }
}
