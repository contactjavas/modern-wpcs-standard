<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Arrays;

class ArraysFixture {
	public function doSomething() {
		// Next line should report no long arrays allowed
		$rest = ['x' => 'y'];
		$rest;
	}

	public function moreCases(array $x): array {
		$a = [ 1 ];
		$b = [ 1 ];
		$c = [ 1 ];
		$d = [ [ 1 ] ];
		$e = array /* keep */ ( 1 );
		return [$a, $b, $c, $d, $e, $x];
	}
}
