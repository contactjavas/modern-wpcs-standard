<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Conditions;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class DisallowConditionAssignWithoutConditionalSniffTest extends TestCase {
	public function testDisallowConditionAssignWithoutConditionalSniff() {
		$fixtureFile = __DIR__ . '/ConditionsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Conditions/DisallowConditionAssignWithoutConditionalSniff.php';
		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([5, 9], $lines);
	}
}
