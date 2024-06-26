<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Constants;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class DisallowDefineSniffTest extends TestCase {
	public function testDisallowDefineSniff() {
		$fixtureFile = __DIR__ . '/ConstantsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Constants/DisallowDefineSniff.php';
		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([4, 8], $lines);
	}
}
