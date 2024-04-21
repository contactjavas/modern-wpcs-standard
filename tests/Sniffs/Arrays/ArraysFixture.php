<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Arrays;

class ArraysFixture {
	public function doSomething() {
		// Next line should report no long arrays allowed
		$rest = array('x' => 'y');
		$rest;
	}
}
