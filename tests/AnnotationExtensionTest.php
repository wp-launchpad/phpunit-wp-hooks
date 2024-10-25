<?php

namespace WPLaunchpadPHPUnitWPHooks\Tests;

class AnnotationExtensionTest extends TestCase
{

    /**
     * @hook-isolated rucss-hook rucss
     * @hook-isolated preload-hook preload 15
     */
    public function testAnnotation()
    {
        global $wp_filter;

        $filters = [
            'rucss-hook' => [
                'priority' => 10,
                'method'   => 'rucss',
                'parameters' => 0
            ],
            'launchpad-hook' => [
                'priority' => 10,
                'method'   => 'preload',
                'parameters' => 2
            ],
            'preload-hook' => [
                'priority' => 15,
                'method'   => 'preload',
                'parameters' => 2
            ],
        ];

        foreach ($filters as $filter => $configs) {
            $this->assertRightCallback($wp_filter[ $filter ]->callbacks, $configs['method'], $configs['priority'], $configs['parameters']);
        }

        $this->assertCount(1, $this->array_flatten($wp_filter['rucss-hook']->callbacks));
        $this->assertCount(1, $this->array_flatten($wp_filter['preload-hook']->callbacks));
    }

    protected function assertRightCallback($callbacks, $method, $priority, $parameters) {
        foreach ( $callbacks[$priority] as $key => $config ) {
            // Skip if not this tests callback.
            if ( substr( $key, - strlen( $method ) ) !== $method ) {
                continue;
            }


            $this->assertSame($parameters, $config['accepted_args']);
        }
    }

    protected function array_flatten($array) {
        return call_user_func_array('array_merge', $array);
    }

    /**
     * @hook rucss-hook
     *
     * @return void
     */
    public function rucss(){}

    /**
     * @hook rucss-hook
     *
     * @return void
     */
    public function rucss2(){}

    /**
     * @hook preload-hook 15
     * @hook $prefix-hook
     */
    public function preload($path, $config){}

    /**
     * @hook preload-hook 20
     */
    public function preload2($path, $config){}
}