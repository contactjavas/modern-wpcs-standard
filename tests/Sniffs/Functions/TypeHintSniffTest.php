<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Functions;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class TypeHintSniffTest extends TestCase {
	public function testTypeHintSniff() {
		$fixtureFile = __DIR__ . '/FunctionsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/TypeHintSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$errorLines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$warningLines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([143, 199, 203], $errorLines);
		$this->assertEquals([123, 128, 133, 138], $warningLines);
	}

	public function testTypeHintSniffWithVariadicArgs() {
		$fixtureFile = __DIR__ . '/VariadicArgumentsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/TypeHintSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([15, 24, 29, 34], $lines);
	}

	public function testTypeHintSniffWithClosures() {
		$fixtureFile = __DIR__ . '/ClosureFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/TypeHintSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$warningLines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$errorLines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([10, 16], $warningLines);
		$this->assertEquals([31, 38], $errorLines);
	}

	public function testTypeHintSniffWithInterface() {
		$fixtureFile = __DIR__ . '/InterfaceFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/TypeHintSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([], $lines);
	}

	public function testTypeHintSniffWithAbstractClass() {
		$fixtureFile = __DIR__ . '/AbstractClassFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/TypeHintSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([16], $lines);
	}

	public function testTypeHintSniffReturnTypeEdgeCases() {
		$fixtureFile = __DIR__ . '/ReturnTypeEdgeCasesFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/TypeHintSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$this->assertEquals([57, 62, 69, 76], $helper->getErrorLineNumbersFromFile($phpcsFile));
		$this->assertEquals([], $helper->getWarningLineNumbersFromFile($phpcsFile));

		$sources = [];
		foreach ( $phpcsFile->getErrors() as $line => $columns) {
			foreach ( $columns as $messages) {
				foreach ( $messages as $message) {
					$sources[$line][] = str_replace('ModernWpcsStandard.Functions.TypeHint.', '', $message['source']);
				}
			}
		}
		$this->assertSame(
			[
				57 => ['IncorrectVoidReturn'],
				62 => ['IncorrectVoidReturnType'],
				69 => ['IncorrectVoidReturnType'],
				76 => ['UnusedReturnType'],
			],
			$sources
		);
	}

	public function testTypeHintSniffArgumentTypeEdgeCases() {
		$fixtureFile = __DIR__ . '/ArgumentTypeEdgeCasesFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Functions/TypeHintSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$this->assertEquals([26], $helper->getWarningLineNumbersFromFile($phpcsFile));
		$this->assertCount(1, $phpcsFile->getWarnings()[26], 'Expected exactly one warning column on line 26');
		$this->assertEquals([], $helper->getErrorLineNumbersFromFile($phpcsFile));
	}
}
