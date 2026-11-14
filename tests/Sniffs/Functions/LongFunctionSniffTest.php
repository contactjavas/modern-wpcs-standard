<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Functions;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class LongFunctionSniffTest extends TestCase {
	public function testLongFunctionSniff() {
		$fixtureFile = __DIR__ . '/LongFunctionsFixture.php';
		$sniffFile =
			__DIR__ .
			'/../../../ModernWpcsStandard/Sniffs/Functions/LongFunctionSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs(
			$sniffFile,
			$fixtureFile
		);
		$phpcsFile->process();
		$warnings = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([66], $warnings);
	}

	public function testLongFunctionWithDifferentLength() {
		$fixtureFile = __DIR__ . '/LongFunctionsFixture.php';
		$sniffFile =
			__DIR__ .
			'/../../../ModernWpcsStandard/Sniffs/Functions/LongFunctionSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs(
			$sniffFile,
			$fixtureFile
		);
		$phpcsFile->ruleset->setSniffProperty(
			'ModernWpcsStandard\Sniffs\Functions\LongFunctionSniff',
			'maxFunctionLines',
			['scope' => 'sniff', 'value' => 70]
		);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$expectedLines = [];
		$this->assertEquals($expectedLines, $lines);
	}

	public function testLongFunctionWithShorterLength() {
		$fixtureFile = __DIR__ . '/LongFunctionsFixture.php';
		$sniffFile =
			__DIR__ .
			'/../../../ModernWpcsStandard/Sniffs/Functions/LongFunctionSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs(
			$sniffFile,
			$fixtureFile
		);
		$phpcsFile->ruleset->setSniffProperty(
			'ModernWpcsStandard\Sniffs\Functions\LongFunctionSniff',
			'maxFunctionLines',
			['scope' => 'sniff', 'value' => 2]
		);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$expectedLines = [8, 66];
		$this->assertEquals($expectedLines, $lines);
	}

	public function testOneLineFunctionAfterManyLinesIsNotReported() {
		$content = "<?php\ndeclare( strict_types=1 );\n"
			. str_repeat("\$a = 1;\n", 60)
			. "function short() { return 1; }\n";

		$this->assertEquals([], $this->getWarningLinesForContent($content));
	}

	public function testOneLineFunctionCountsAsOneLine() {
		$content = "<?php\nfunction short() { return 1; }\n";

		$this->assertEquals(
			[2],
			$this->getWarningLinesForContent($content, 0)
		);
		$this->assertEquals(
			[],
			$this->getWarningLinesForContent($content, 1)
		);
	}

	/**
	 * 40 code lines separated by blank lines: at the limit, so no warning, and
	 * 41 code lines: one over. LF and CRLF must behave identically.
	 */
	public function testBlankLinesAreSkippedForLfAndCrlf() {
		foreach (["\n", "\r\n"] as $eol) {
			foreach ([40 => [], 41 => [3]] as $codeLines => $expected) {
				$content = "<?php{$eol}declare( strict_types=1 );{$eol}function f() {{$eol}"
					. str_repeat("\t\$a = 1;{$eol}{$eol}", $codeLines)
					. "}{$eol}";

				$this->assertEquals(
					$expected,
					$this->getWarningLinesForContent($content),
					sprintf('%d code lines, EOL %s', $codeLines, json_encode($eol))
				);
			}
		}
	}

	public function testAbstractAndInterfaceMethodsAreIgnored() {
		$content = "<?php\n"
			. "interface I { public function a(): void; }\n"
			. "abstract class A { abstract public function b(): void; }\n";

		$this->assertEquals([], $this->getWarningLinesForContent($content, 0));
	}

	private function getWarningLinesForContent(
		string $content,
		?int $maxFunctionLines = null
	): array {
		$tempBase = tempnam(sys_get_temp_dir(), 'longfn');
		unlink($tempBase);
		$fixtureFile = $tempBase . '.php';
		file_put_contents($fixtureFile, $content);

		try {
			$sniffFile =
				__DIR__ .
				'/../../../ModernWpcsStandard/Sniffs/Functions/LongFunctionSniff.php';
			$helper = new SniffTestHelper();
			$phpcsFile = $helper->prepareLocalFileForSniffs(
				$sniffFile,
				$fixtureFile
			);

			if ($maxFunctionLines !== null) {
				$phpcsFile->ruleset->setSniffProperty(
					'ModernWpcsStandard\Sniffs\Functions\LongFunctionSniff',
					'maxFunctionLines',
					['scope' => 'sniff', 'value' => $maxFunctionLines]
				);
			}

			$phpcsFile->process();

			return $helper->getWarningLineNumbersFromFile($phpcsFile);
		} finally {
			unlink($fixtureFile);
		}
	}
}
