<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowLongformArraySniff implements Sniff {
	public function register(): array {
		return [T_ARRAY];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// Only `array(...)` literals carry parenthesis pointers; the `array` type keyword does not.
		if ( ! isset($tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer'])) {
			return;
		}

		$error = 'Longform array is not allowed';
		$openParenPtr = $tokens[$stackPtr]['parenthesis_opener'];
		$nonWhitespaceBeforeParen = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, $openParenPtr, true);

		if ( $nonWhitespaceBeforeParen !== false) {
			// A comment sits between `array` and `(`; don't risk mangling it.
			$phpcsFile->addError($error, $stackPtr, 'LongformArray');
			return;
		}

		if ( $phpcsFile->addFixableError($error, $stackPtr, 'LongformArray')) {
			$this->fixTokens($phpcsFile, $stackPtr);
		}
	}

	private function fixTokens(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();
		$openParenPtr = $tokens[$stackPtr]['parenthesis_opener'];
		$closeParenPtr = $tokens[$stackPtr]['parenthesis_closer'];
		$phpcsFile->fixer->beginChangeset();
		for ( $ptr = $stackPtr; $ptr < $openParenPtr; $ptr++) {
			$phpcsFile->fixer->replaceToken($ptr, '');
		}
		$phpcsFile->fixer->replaceToken($openParenPtr, '[');
		$phpcsFile->fixer->replaceToken($closeParenPtr, ']');
		$phpcsFile->fixer->endChangeset();
	}
}
