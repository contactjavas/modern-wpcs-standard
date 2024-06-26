<?php
declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Whitespace;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class DisallowMultipleNewlinesSniffTest extends TestCase {
	public function testDisallowMultipleNewlinesSniff() {
		$fixtureFile = __DIR__ . '/NoMultipleNewlinesFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Whitespace/DisallowMultipleNewlinesSniff.php';
		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([8, 15, 20], $lines);
	}

	public function testFixDisallowMultipleNewlinesSniff() {
		$fixtureFile = __DIR__ . '/NoMultipleNewlinesFixture.php';
		$fixedFixtureFile = __DIR__ . '/FixedMultipleNewlinesFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Whitespace/DisallowMultipleNewlinesSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$actualContents = $helper->getFixedFileContents($phpcsFile);
		$fixedContents = file_get_contents($fixedFixtureFile);
		$this->assertEquals($fixedContents, $actualContents);
	}
}
