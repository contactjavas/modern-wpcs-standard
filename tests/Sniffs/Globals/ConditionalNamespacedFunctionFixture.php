<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Globals;

if ( ! function_exists( __NAMESPACE__ . '\conditional_namespaced' ) ) {
	function conditional_namespaced() {
		return namespace\helper();
	}
}

try {
	function in_try() {
	}
} catch ( \Throwable $e ) {
}

class ConditionalNamespacedFunctionFixture {
	public function method() {
		if ( true ) {
			$closure = function () {
			};
		}
	}
}
