<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\Globals;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class DisallowGlobalFunctionsSniffTest extends TestCase {
	public function testDisallowGlobalFunctionsSniff() {
		$fixtureFile = __DIR__ . '/GlobalsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Globals/DisallowGlobalFunctionsSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([6], $lines);
	}

	public function testDisallowGlobalFunctionsSniffAllowsNamespacedGlobals() {
		$fixtureFile = __DIR__ . '/NamespacedGlobalFunctionsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Globals/DisallowGlobalFunctionsSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([], $lines);
	}

	public function testDisallowGlobalFunctionsSniffFlagsConditionalGlobals() {
		$fixtureFile = __DIR__ . '/ConditionalGlobalFunctionFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Globals/DisallowGlobalFunctionsSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([7], $lines);
	}

	public function testDisallowGlobalFunctionsSniffAllowsConditionalNamespacedFunctions() {
		$fixtureFile = __DIR__ . '/ConditionalNamespacedFunctionFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Globals/DisallowGlobalFunctionsSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([], $lines);
	}

	public function testDisallowGlobalFunctionsSniffFlagsGlobalNamespaceBlock() {
		$fixtureFile = __DIR__ . '/BracedNamespacesFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/Globals/DisallowGlobalFunctionsSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
		$this->assertEquals([17], $lines);
	}
}
