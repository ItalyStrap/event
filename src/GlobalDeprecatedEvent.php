<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * An event name no longer used, a filter or an action: WordPress stores both the same way.
 *
 * @psalm-api
 */
final class GlobalDeprecatedEvent
{
    private string $name;
    private string $version;
    private string $replacement;
    private string $message;

    /**
     * @var list<mixed>
     */
    private array $args;

    /**
     * @param string $name The event name no longer used.
     * @param string $version The version that stopped using it.
     * @param string $replacement The event name to use instead, empty for none.
     * @param string $message Why it is no longer used, or what to do instead.
     * @param list<mixed> $args The arguments its listeners get, one for each argument they take.
     */
    public function __construct(
        string $name,
        string $version,
        string $replacement = '',
        string $message = '',
        array $args = []
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->replacement = $replacement;
        $this->message = $message;
        $this->args = $args;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function replacement(): string
    {
        return $this->replacement;
    }

    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return list<mixed>
     */
    public function args(): array
    {
        return $this->args;
    }
}
