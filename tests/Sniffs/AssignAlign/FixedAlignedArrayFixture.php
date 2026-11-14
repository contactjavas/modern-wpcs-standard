<?php

declare( strict_types=1 );

$myAry = [
	// Next two lines should report
	'one' => 'one',
	'two' => 'two',
	'three' => 'three',
];

$myAry = [
	// This comment    should be fine.
	'one' => 'one',
	'two' => 'two',
	'three' => 'three',
];

$myAry = [
	 'one' => 'one',
	 'two' => 'two',
	 'three' => 'three',
];

// Leading operators on their own indented line are fine.
$myAry = [
	'key'
		=> 'value',
	'other'
		=> 'value',
];

$myVar
	= 'value';
