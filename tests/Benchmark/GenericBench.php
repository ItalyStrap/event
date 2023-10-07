<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Benchmarks;

/**
 * @BeforeMethods({"setUp"})
 */
class GenericBench
{
    public function setUp(): void
    {
    }

    /**
     * @revs (10000)
     * @iterations (5)
     */
    public function benchCallUserFunc(): void
    {
        \call_user_func(fn() => 'Value printed');
    }

    /**
     * @revs (10000)
     * @iterations (5)
     */
    public function benchCallUserFuncArray(): void
    {
        \call_user_func_array(fn() => 'Value printed', []);
    }

    /**
     * @revs (10000)
     * @iterations (5)
     */
    public function benchCallUserFuncArrayWithFiveArgs(): void
    {
        \call_user_func_array(fn($arg1, $arg2, $arg3, $arg4, $arg5) => 'Value printed', [1, 2, 3, 4, 5]);
    }
}
