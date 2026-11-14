<?php

declare( strict_types=1 );

// Next line should report no define allowed
define("FOO", "bar");

function test() {
	// Next line should report no define allowed
	define("FOO", "bar");
}

\define("FOO", "bar");
DEFINE("FOO", "bar");
Define ("FOO", "bar");
$o->define("FOO");
$o?->define("FOO");
Bar::define("FOO");
new Define("FOO");
Foo\define("FOO", "bar");

class DefineHolder {
	const define = 1;

	public function define() {
	}
}
