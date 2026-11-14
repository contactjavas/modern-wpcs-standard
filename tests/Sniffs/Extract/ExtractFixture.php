<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Extract;

class ExtractFixture {
	public function doSomething() {
		$all = ['foo' => 'bar' ];
		// Next line should report no extract allowed
		extract($all);
	}

	public function extract() {
		$extract = 'foo';
		$extract;
	}
}

$all = [];
\extract($all);
EXTRACT($all);
Extract ( $all );
$o->extract($all);
$o?->extract($all);
Foo::extract($all);
new Extract($all);
Foo\extract($all);
namespace\extract($all);
$o-> /* comment */ extract($all);
