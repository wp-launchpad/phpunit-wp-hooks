<?php

namespace WPLaunchpadPHPUnitWPHooks;

use PHPUnit\Util\Test;
use ReflectionMethod;
use WPLaunchpadPHPUnitWPHooks\Data\Callback;
use WPLaunchpadPHPUnitWPHooks\Data\Isolated;

trait MockHooks
{
    use IsolateHookTrait;

	private $initial_options = [];

    public function mockHooks(): void
    {
		$this->initial_options = [];
        $callbacks = $this->getCallbacks();
        foreach ($callbacks as $callback) {
            $hook = $this->addPrefix($callback->getHook());
            add_filter($hook, [$this, $callback->getCallback()], $callback->getPriority(), $callback->getParameters());
        }

        $isolatedHooks = $this->getIsolated();
        foreach ($isolatedHooks as $isolatedHook) {
            $hook = $this->addPrefix($isolatedHook->getHook());
            $this->unregisterAllCallbacksExcept($hook, $isolatedHook->getMethod(), $isolatedHook->getPriority());
        }
    }

    public function resetHooks(): void
    {
		global $wpdb;

        $isolatedHooks = $this->getIsolated();
        foreach ($isolatedHooks as $isolatedHook) {
            $hook = $this->addPrefix($isolatedHook->getHook());
            $this->restoreWpHook($hook);
        }

        $callbacks = $this->getCallbacks();
        foreach ($callbacks as $callback) {
            $hook = $this->addPrefix($callback->getHook());
            remove_filter($hook, [$this, $callback->getCallback()], $callback->getPriority());
        }

		foreach ($this->initial_options as $option => $value) {
			if($value === false) {
				delete_option($option);
				continue;
			}
			update_option($option, $value);
		}

		$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%'");

		wp_cache_flush();

		$this->initial_options = [];
    }

    /**
     * @return Callback[]
     * @throws \ReflectionException
     */
    protected function getCallbacks(): array
    {
        $class = get_class($this);

        $callbacks = [];
        $methods = get_class_methods($class);
        foreach ($methods as $method) {
            $annotations = Test::parseTestMethodAnnotations($class, $method);
            if (! key_exists('method', $annotations) || ! is_array($annotations['method']) || ! key_exists('hook', $annotations['method'])) {
                continue;
            }

            foreach ($annotations['method']['hook'] as $annotation) {
                $callback = new Callback();

                $callback->setCallback($method);
                $reflectedMethod = new ReflectionMethod($class, $method);
                $callback->setParameters($reflectedMethod->getNumberOfParameters());

                $parts = explode(' ', $annotation);

                $callback->setHook(array_shift($parts));

                $priority = array_shift($parts);
                if($priority !== null) {
                    $callback->setPriority((int) $priority);
                }

                $callbacks []= $callback;
            }

        }

        return $callbacks;
    }

    /**
     * @return Isolated[]
     */
    protected function getIsolated()
    {
        $class = get_class($this);

        $isolatedHooks = [];

        $parts = explode(' ', $this->getCurrentTest());
        $testedMethod = array_shift($parts);

        $annotations = Test::parseTestMethodAnnotations($class, $testedMethod);
        if (! key_exists('method', $annotations) || ! is_array($annotations['method']) || ! key_exists('hook-isolated', $annotations['method'])) {
            return $isolatedHooks;
        }
        foreach ($annotations['method']['hook-isolated'] as $annotation) {
            $isolated = new Isolated();
            $parts = explode(' ', $annotation);
            $isolated->setHook(array_shift($parts));
            $method = array_shift($parts);
            if($method) {
                $isolated->setMethod($method);
            }
            $priority = array_shift($parts);
            if($priority !== null) {
                $isolated->setPriority((int) $priority);
            }

            $isolatedHooks []= $isolated;
        }
        return $isolatedHooks;
    }

    protected function addPrefix(string $hook): string
    {
        return str_replace('$prefix', $this->getPrefix(), $hook);
    }

	/**
	 * @hook pre_update_option
	 */
	public function register_original_option_value_after_update($value, $name) {
		if(key_exists($name, $this->initial_options)) {
			return $value;
		}

		$this->initial_options[$name] = get_option($name);

		return $value;
	}

	/**
	 * @hook delete_option
	 */
	public function register_original_option_value_after_delete($name) {
		if(key_exists($name, $this->initial_options)) {
			return;
		}

		$this->initial_options[$name] = get_option($name);
	}

    protected function getPrefix(): string {
        return '';
    }

    abstract protected function getCurrentTest(): string;
}