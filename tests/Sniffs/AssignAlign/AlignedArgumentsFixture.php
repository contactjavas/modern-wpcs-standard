<?php

declare( strict_types=1 );

function myFunc(
	$one,
	// Next line should report
	$two = 'two',
	$three = 'three'
	// This comment    should be fine.
) {
}
