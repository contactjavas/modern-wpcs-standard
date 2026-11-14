<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Globals\Braced {
	function namespaced() {
	}

	if ( ! function_exists( 'conditional_namespaced' ) ) {
		function conditional_namespaced() {
		}
	}
}

namespace {
	// Next line should report no global functions allowed
	function global_block() {
	}
}
