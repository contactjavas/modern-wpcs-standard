<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Functions;

class ReturnTypeEdgeCasesFixture {
	public function neverReturns(): never {
		throw new \Exception('x');
	}

	public function yieldFrom(): \Generator {
		yield from [1, 2];
	}

	public function bareYieldAndBareReturn(): \Generator {
		yield;
		return;
	}

	public function anonClass(): void {
		$x = new class {
			public function get(): int {
				return 1;
			}
		};
	}

	public function nestedFunction(): void {
		function nestedReturnsValue(): int {
			return 1;
		}
	}

	public function arrowFunction(): void {
		$fn = fn(): int => 1;
	}

	public function attribute(#[Attr(name: 'x')] int $a): int {
		return $a;
	}

	public function closureWithUse(): void {
		$b = 1;
		$fn = function (int $a) use ($b): int {
			return $a + $b;
		};
	}

	public function nullableUnion(): ?int {
		return 1;
	}

	// Positive controls below.

	// Next line should report IncorrectVoidReturn
	public function bareReturnWithIntType(): int {
		return;
	}

	// Next line should report IncorrectVoidReturnType
	public function valueReturnWithVoidType(): void {
		return 1;
	}

	public function anonClassWithBadInnerMethod(): void {
		$x = new class {
			// Next line should report IncorrectVoidReturnType
			public function get(): void {
				return 1;
			}
		};
	}

	// Next line should report UnusedReturnType
	public function closureReturnDoesNotCount(): int {
		$fn = function (): int {
			return 1;
		};
	}
}
