<?php

namespace WPLaunchpadPHPUnitWPHooks\Tests;

use WPLaunchpadPHPUnitWPHooks\Tests\TestCase;

class ResetEnvironnementTest extends TestCase {
	public function testShouldResetOptions() {

		$initial_options = [
			'test' => null,
			'test2' => 'value'
		];

		foreach ($initial_options as $option => $value) {
			update_option($option, $value);
		}

		$this->mockHooks();

		update_option('test', 'my_value');
		update_option('test', 'my_value');


		$this->resetHooks();

		foreach ($initial_options as $option => $value) {
			$this->assertSame($value, get_option($option));
		}
	}
}