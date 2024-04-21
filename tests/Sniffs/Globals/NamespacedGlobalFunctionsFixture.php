<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Globals;

function test() {
	define("FOO", "bar");
}

class NamespacedGlobalFunctionsFixture {
	public function notTooLong() {
		$foo = 'bar';
		$foo;
		$foo; // Hello
	}
}
