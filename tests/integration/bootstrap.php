<?php

declare(strict_types=1);

if (!\function_exists('did_filter')) {
    function did_filter(string $hook_name): int
    {
        global $wp_filters;

        if (! isset($wp_filters[ $hook_name ])) {
            return 0;
        }

        return $wp_filters[ $hook_name ];
    }
}
