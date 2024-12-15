<?php

namespace WPLaunchpadPHPUnitWPHooks;

use ReflectionClass;
use ReflectionException;

trait IsolateHookTrait
{
    protected $original_wp_filter;

    protected $original_wp_priorities;

    protected function unregisterAllCallbacksExcept( $event_name, $method_name, $priority = 10 ) {
        global $wp_filter;
        $this->original_wp_filter[ $event_name ] = $wp_filter[ $event_name ]->callbacks;

        foreach ( $this->original_wp_filter[ $event_name ][ $priority ] as $key => $config ) {

            // Skip if not this tests callback.
            if ( substr( $key, - strlen( $method_name ) ) !== $method_name ) {
                continue;
            }

			if(key_exists($priority, $callbacks)) {
				$callbacks = [
					$priority => array_merge_recursive($callbacks[$priority], [ $key => $config ])
				];
			}else {
				$callbacks = [
					$priority => [ $key => $config ],
				];
			}

			$wp_filter[ $event_name ]->callbacks = $callbacks;
        }

        try {
            $wp_hooks = $wp_filter[ $event_name ];
            $reflection = new ReflectionClass($wp_hooks);
            $priorities_property = $reflection->getProperty('priorities');
            $priorities_property->setAccessible(true);
            $this->original_wp_priorities[ $event_name ] = $priorities_property->getValue($wp_hooks);
            $priorities = $priorities_property->getValue($wp_hooks);
        } catch (ReflectionException $e) {
            return;
        }

        foreach ($this->original_wp_priorities[ $event_name ] as $priority) {
            if ( key_exists($priority, $wp_filter[ $event_name ]->callbacks)) {
                continue;
            }

            $priorities = array_values(array_filter($priorities, function ($current) use ($priority) {
                return $current !== $priority;
            }));

        }

        $priorities_property->setValue($wp_hooks, $priorities);
    }

    protected function restoreWpHook($event_name ) {
        global $wp_filter;
        $wp_filter[ $event_name ]->callbacks = $this->original_wp_filter[ $event_name ];
        if (! $this->original_wp_priorities[ $event_name ]) {
            return;
        }
        $wp_hooks = $wp_filter[ $event_name ];
        $reflection = new ReflectionClass($wp_hooks);
        $priorities_property = $reflection->getProperty('priorities');
        $priorities_property->setAccessible(true);
        $priorities_property->setValue($wp_hooks, $this->original_wp_priorities[ $event_name ]);
    }
}