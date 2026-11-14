<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Tests\Sniffs\Conditions;

class ConditionsFixture {
	public function comparisonTest() {
		// Next line should report no condition assignment without comparison
		if ($actual = $this->getActual()) {
			echo $actual;
		}

		// Next line should report no condition assignment without comparison
		if (
			$actual = $this->getActual()
		) {
			echo $actual;
		}

		if ($actual = $this->getActual() == true) {
			echo $actual;
		}

		if ($actual = $this->getActual() > 3) {
			echo $actual;
		}

		if ($actual = $this->getActual() < 3) {
			echo $actual;
		}

		if ($actual = $this->getActual() <= 3) {
			echo $actual;
		}

		if ($actual = $this->getActual() >= 3) {
			echo $actual;
		}

		if ($actual = $this->getActual() === 3) {
			echo $actual;
		}

		if ($actual = $this->getActual() != 3) {
			echo $actual;
		}

		if ($actual = $this->getActual() !== 3) {
			echo $actual;
		}

		if (true == $actual = $this->getActual()) {
			echo $actual;
		}

		if ($actual) {
			return 'yo';
		}
	}

	public function getActual() {
		return 'hello';
	}

	public function scopeTest() {
		if (false) {
			return;
		// Next line should report no condition assignment without comparison
		} elseif ($actual = $this->getActual()) {
			echo $actual;
		}

		// Next line should report no condition assignment without comparison
		while ($actual = $this->getActual()) {
			echo $actual;
		}

		do {
			echo 'loop';
		// Next line should report no condition assignment without comparison
		} while ($actual = $this->getActual());

		while (($actual = $this->getActual()) !== false) {
			echo $actual;
		}

		do {
			echo 'loop';
		} while (($actual = $this->getActual()) !== false);

		// Next line should report no condition assignment without comparison
		if ($actual = array_filter([1, 2], fn ($x) => $x > 1)) {
			echo $actual;
		}

		if (array_filter([1, 2], function ($x = 1) {
			return $x;
		})) {
			echo 'no assignment in the condition itself';
		}

		if ($actual === 'hello') {
			$actual = 'world';
		}
	}
}
