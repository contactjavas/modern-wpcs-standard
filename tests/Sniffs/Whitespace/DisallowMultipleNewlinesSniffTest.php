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

	public function testCommentsFollowedByBlankLines() {
		$phpcsFile = $this->processFixture(__DIR__ . '/CommentNewlinesFixture.php');
		$this->assertEquals([10, 19], (new SniffTestHelper())->getErrorLineNumbersFromFile($phpcsFile));
		$this->assertCount(1, $phpcsFile->getErrors()[10]);
		$this->assertCount(1, $phpcsFile->getErrors()[19]);
	}

	public function testFixCommentsFollowedByBlankLines() {
		$phpcsFile = $this->processFixture(__DIR__ . '/CommentNewlinesFixture.php');
		$actualContents = (new SniffTestHelper())->getFixedFileContents($phpcsFile);
		$fixedContents = file_get_contents(__DIR__ . '/FixedCommentNewlinesFixture.php');
		$this->assertEquals($fixedContents, $actualContents);
	}

	public function testSingleBlankLineAfterCommentsIsUnchangedByFixer() {
		$fixtureFile = __DIR__ . '/SingleBlankLineAfterCommentsFixture.php';
		$phpcsFile = $this->processFixture($fixtureFile);
		$this->assertEquals([], (new SniffTestHelper())->getErrorLineNumbersFromFile($phpcsFile));
		$actualContents = (new SniffTestHelper())->getFixedFileContents($phpcsFile);
		$this->assertEquals(file_get_contents($fixtureFile), $actualContents);
	}

	public function testCrlfLineEndings() {
		$lf = str_replace("\r\n", "\n", file_get_contents(__DIR__ . '/CommentNewlinesFixture.php'));
		$fixedLf = str_replace("\r\n", "\n", file_get_contents(__DIR__ . '/FixedCommentNewlinesFixture.php'));
		$tempFile = sys_get_temp_dir() . '/mwcs-crlf-' . uniqid() . '.php';
		file_put_contents($tempFile, str_replace("\n", "\r\n", $lf));
		try {
			$phpcsFile = $this->processFixture($tempFile);
			$helper = new SniffTestHelper();
			$this->assertEquals([10, 19], $helper->getErrorLineNumbersFromFile($phpcsFile));
			$this->assertEquals(str_replace("\n", "\r\n", $fixedLf), $helper->getFixedFileContents($phpcsFile));
		} finally {
			unlink($tempFile);
		}
	}

	private function processFixture(string $fixtureFile) {
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Whitespace/DisallowMultipleNewlinesSniff.php';
		$phpcsFile = (new SniffTestHelper())->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		return $phpcsFile;
	}
}
