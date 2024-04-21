<?php
declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Whitespace;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class RequireNewlineBetweenFunctionsSniffTest extends TestCase {
	public function testRequireNewlineBetweenFunctionsSniff() {
		$fixtureFile = __DIR__ . '/NewlinesBetweenFunctionsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Whitespace/RequireNewlineBetweenFunctionsSniff.php';
		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([5, 14, 30, 52, 61, 70, 76], $lines);
	}

	public function testFixRequireNewlineBetweenFunctionsSniff() {
		$fixtureFile = __DIR__ . '/NewlinesBetweenFunctionsFixture.php';
		$fixedFixtureFile = __DIR__ . '/FixedNewlinesBetweenFunctionsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Whitespace/RequireNewlineBetweenFunctionsSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$actualContents = $helper->getFixedFileContents($phpcsFile);
		$fixedContents = file_get_contents($fixedFixtureFile);
		$this->assertEquals($fixedContents, $actualContents);
	}
}
