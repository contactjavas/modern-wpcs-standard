<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\StrictTypes;

function test() {
	define("FOO", "bar");
}

class OneLineStrictTypesFixture {
	public function notTooLong() {
		$foo = 'bar';
		$foo;
		$foo; // Hello
	}
}
