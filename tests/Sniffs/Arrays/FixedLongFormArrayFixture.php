<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Arrays;

class FixedLongFormArrayFixture {
	public function doSomething() {
		// Next line should report no long arrays allowed
		$rest = ['x' => 'y'];
		$rest;
	}
}
