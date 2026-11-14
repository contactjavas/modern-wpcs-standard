<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Arrays;

class ArraysFixture {
	public function doSomething() {
		// Next line should report no long arrays allowed
		$rest = array('x' => 'y');
		$rest;
	}

	public function moreCases(array $x): array {
		$a = Array( 1 );
		$b = ARRAY( 1 );
		$c = array ( 1 );
		$d = array( array( 1 ) );
		$e = array /* keep */ ( 1 );
		return [$a, $b, $c, $d, $e, $x];
	}
}
