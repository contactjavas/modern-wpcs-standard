<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Functions;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class DisallowCallUserFuncSniffTest extends TestCase {
	public function testDisallowCallUserFuncSniff() {
		$fixtureFile = __DIR__ . '/CallUserFuncFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/DisallowCallUserFuncSniff.php';
		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([7, 10], $lines);
	}
}
