<?php

use WPLaunchpadPHPUnitWPHooks\MockHooks;

class AnnotationExtensionTest extends \PHPUnit\Framework\TestCase
{
    use MockHooks;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockHooks();
    }

    protected function tearDown(): void
    {
        $this->resetHooks();
        parent::tearDown();
    }

    public function testAnnotation()
    {

    }

    /**
     * @hook rucss-hook
     * @hook-priority 10
     * @hook-isolate
     *
     * @return void
     */
    public function rucss()
    {

    }

    function getPrefix(): string
    {
        return 'launchpad';
    }

    function getCurrentTest(): string
    {
        return $this->getName();
    }
}