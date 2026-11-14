<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Whitespace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class RequireNewlineBetweenFunctionsSniff implements Sniff {
	public function register(): array {
		return [T_FUNCTION];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// Signature-only (abstract / interface) functions have no body.
		if ( ! isset($tokens[$stackPtr]['scope_closer'])) {
			return;
		}

		$endofFuncPtr = $tokens[$stackPtr]['scope_closer'];
		$endOfLinePtr = $this->getNextNewlinePtr($phpcsFile, $endofFuncPtr);

		if ( $endOfLinePtr === false || ! isset($tokens[$endOfLinePtr + 1])) {
			return;
		}

		$nextToken = $tokens[$endOfLinePtr + 1];

		if ( str_ends_with($nextToken['content'], "\n")) {
			return;
		}

		// Only trigger if the next line contains a function definition
		// or a comment
		$nextEndOfLinePtr = $this->getNextNewlinePtr($phpcsFile, $endofFuncPtr + 1);
		$forbiddenTypes = [
			T_FUNCTION,
			T_COMMENT,
		];
		$nextForbiddenToken = $phpcsFile->findNext($forbiddenTypes, $endOfLinePtr + 1, null, false, null, true);

		if ( $nextForbiddenToken === false || $nextForbiddenToken > $nextEndOfLinePtr) {
			return;
		}

		$error = 'Functions must be separated by a blank line';
		$shouldFix = $phpcsFile->addFixableError($error, $endofFuncPtr, 'MissingNewline');

		if ( $shouldFix) {
			$phpcsFile->fixer->beginChangeset();
			$phpcsFile->fixer->addNewline($endofFuncPtr);
			$phpcsFile->fixer->endChangeset();
		}
	}

	private function getNextNewlinePtr(File $phpcsFile, int $stackPtr): int|false {
		$tokens = $phpcsFile->getTokens();
		$ptr = $stackPtr;
		while ( ( $ptr = $phpcsFile->findNext(T_WHITESPACE, $ptr + 1) ) !== false) {
			if ( str_contains($tokens[$ptr]['content'], "\n")) {
				return $ptr;
			}
		}
		return false;
	}
}
