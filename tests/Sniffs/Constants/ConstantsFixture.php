<?php

declare( strict_types=1 );

// Next line should report no define allowed
define("FOO", "bar");

function test() {
	// Next line should report no define allowed
	define("FOO", "bar");
}
