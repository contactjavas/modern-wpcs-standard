<?php

declare(strict_types=1);

if ( ! function_exists( 'conditional_global' ) ) {
	// Next line should report no global functions allowed
	function conditional_global() {
	}
}

class ConditionalGlobalFunctionFixture {
	public function method() {
		if ( true ) {
			$closure = function () {
			};
		}
	}
}
