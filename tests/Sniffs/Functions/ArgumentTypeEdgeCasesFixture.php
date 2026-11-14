<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Functions;

class ArgumentTypeEdgeCasesFixture extends \stdClass {
	public function __construct(
		public int $typedPromoted,
		public readonly string $typedReadonlyPromoted,
	) {
	}

	public function nullable(string|null $a, int|false $b, array &$c, parent $d): void {
	}

	public function dnf((\Countable&\Traversable)|null $x, true $y, ?self $z): void {
	}

	public function defaultsAndAttributes(#[Attr(name: 'x')] int $a, int $b = self::FOO): void {
	}
}

class ArgumentTypeEdgeCasesPromotedFixture {
	// Next line should warn about no type hint (promoted untyped property)
	public function __construct(
		public $untypedPromoted,
	) {
	}
}
