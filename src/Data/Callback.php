<?php

namespace WPLaunchpadPHPUnitWPHooks\Data;

class Callback
{
    /**
     * @var string
     */
    protected $hook = '';

    /**
     * @var string
     */
    protected $callback = '';

    /**
     * @var int
     */
    protected $parameters = 0;

    /**
     * @var int
     */
    protected $priority = 10;

    public function getHook(): string
    {
        return $this->hook;
    }

    public function setHook(string $hook): void
    {
        $this->hook = $hook;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getParameters(): int
    {
        return $this->parameters;
    }

    public function setParameters(int $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function setCallback(string $callback): void
    {
        $this->callback = $callback;
    }

}