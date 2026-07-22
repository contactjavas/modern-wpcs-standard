<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Whitespace;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowMultipleNewlinesSniff implements Sniff {
	public function register(): array {
		return [T_WHITESPACE];
	}

	public function process(File $phpcsFile, mixed $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		if ( str_ends_with($tokens[$stackPtr]['content'], "\n") === false) {
			return;
		}
		
		if ( $stackPtr < 3) {
			return;
		}
		
		if ( str_ends_with($tokens[$stackPtr - 1]['content'], "\n") === false) {
			return;
		}
		
		if ( str_ends_with($tokens[$stackPtr - 2]['content'], "\n") === false) {
			return;
		}
		
		$error = 'Multiple adjacent blank lines are not allowed';
		$shouldFix = $phpcsFile->addFixableError($error, $stackPtr, 'MultipleNewlines');
		
		if ( $shouldFix) {
			$this->fixTokens($phpcsFile, $stackPtr);
		}
	}

	private function fixTokens(File $phpcsFile, int $stackPtr): void {
		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($stackPtr, '');
		$phpcsFile->fixer->endChangeset();
	}
}
