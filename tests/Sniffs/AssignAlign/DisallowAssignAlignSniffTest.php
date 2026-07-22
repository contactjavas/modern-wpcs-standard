<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\AssignAlign;

use ModernWpcsStandard\Tests\SniffTestHelper;
use PHPUnit\Framework\TestCase;

class DisallowAssignAlignSniffTest extends TestCase {
	public function testDisallowAlignedAssignments() {
		$fixtureFile = __DIR__ . '/AlignedAssignmentsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/AssignAlign/DisallowAssignAlignSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([6,7,8,12,13,17,18,21,22], $lines);
	}

	public function testDisallowAlignedArrays() {
		$fixtureFile = __DIR__ . '/AlignedArrayFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/AssignAlign/DisallowAssignAlignSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([7,8], $lines);
	}

	public function testDisallowAlignedArguments() {
		$fixtureFile = __DIR__ . '/AlignedArgumentsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/AssignAlign/DisallowAssignAlignSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$lines = $helper->getWarningLineNumbersFromFile($phpcsFile);
		$this->assertEquals([8], $lines);
	}

	public function testDisallowAlignedArgumentsFix() {
		$fixtureFile = __DIR__ . '/AlignedArgumentsFixture.php';
		$fixedFixtureFile = __DIR__ . '/FixedAlignedArgumentsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/AssignAlign/DisallowAssignAlignSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$actualContents = $helper->getFixedFileContents($phpcsFile);
		$fixedContents = file_get_contents($fixedFixtureFile);
		$this->assertEquals($fixedContents, $actualContents);
	}

	public function testDisallowAlignedAssignmentsFix() {
		$fixtureFile = __DIR__ . '/AlignedAssignmentsFixture.php';
		$fixedFixtureFile = __DIR__ . '/FixedAlignedAssignmentsFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/AssignAlign/DisallowAssignAlignSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$actualContents = $helper->getFixedFileContents($phpcsFile);
		$fixedContents = file_get_contents($fixedFixtureFile);
		$this->assertEquals($fixedContents, $actualContents);
	}

	public function testDisallowAlignedArraysFix() {
		$fixtureFile = __DIR__ . '/AlignedArrayFixture.php';
		$fixedFixtureFile = __DIR__ . '/FixedAlignedArrayFixture.php';
		$sniffFile = __DIR__ . '/../../../ModernWpcsStandard/Sniffs/AssignAlign/DisallowAssignAlignSniff.php';

		$helper = new SniffTestHelper();
		$phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
		$phpcsFile->process();
		$actualContents = $helper->getFixedFileContents($phpcsFile);
		$fixedContents = file_get_contents($fixedFixtureFile);
		$this->assertEquals($fixedContents, $actualContents);
	}
}
