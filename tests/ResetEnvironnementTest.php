<?php

namespace WPLaunchpadPHPUnitWPHooks\Tests;

class ResetEnvironnementTest extends TestCase {
	public function testShouldResetOptions() {

		$initial_options = [
			'test' => false,
			'test2' => 'value'
		];

		foreach ($initial_options as $option => $value) {
			update_option($option, $value);
		}

		$this->mockHooks();

		update_option('test', 'my_value');
		update_option('test2', 'my_value');


		$this->resetHooks();

		foreach ($initial_options as $option => $value) {

			$this->assertSame($value, get_option($option));
		}
	}

	public function testShouldClearTransients() {
		$initial_transients = [
			'test' => [
				'value' => true,
				'ttl' => 123456789
			],
			'test2' => [
				'value' => 'value',
				'ttl' => 0
			]
		];

		foreach ($initial_transients as $transient => $value) {
			set_transient($transient, $value['value'], $value['ttl']);
		}

		$this->mockHooks();

		set_transient('test', 'my_value', 456789);
		set_transient('test2', 'my_value', 456789);


		$this->resetHooks();

		foreach ($initial_transients as $transient => $value) {
			$this->assertSame(false, get_transient($transient));
		}
	}
}