<?php
namespace WPLaunchpadPHPUnitWPHooks\Tests;

use WPLaunchpadPHPUnitWPHooks\MockHooks;

class TestCase extends \WPMedia\PHPUnit\Integration\TestCase
{
    use MockHooks;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockHooks();
    }

    public function tearDown(): void
    {
        $this->resetHooks();
        parent::tearDown();
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