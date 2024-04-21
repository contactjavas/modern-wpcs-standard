<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Globals;

// Next line should report no global functions allowed
function test() {
	define("FOO", "bar");
}

class GlobalsFixture {
	public function notTooLong() {
		$foo = 'bar';
		$foo;
		$foo; // Hello
	}
}
