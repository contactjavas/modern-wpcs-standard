<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Extract;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class DisallowExtractSniffTest extends TestCase {
	public function testDisallowExtractSniff() {
		$fixtureFile = __DIR__ . '/ExtractFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Extract/DisallowExtractSniff.php';
		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([7], $lines);
	}
}
