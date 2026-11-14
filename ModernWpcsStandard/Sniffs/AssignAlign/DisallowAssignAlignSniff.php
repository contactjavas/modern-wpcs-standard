<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\AssignAlign;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class DisallowAssignAlignSniff implements Sniff {
	public function register(): array {
		return [T_WHITESPACE];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// If the next non-whitespace after multiple spaces is an assignment operator or double arrow, mark a warning
		if ( strlen($tokens[$stackPtr]['content']) <= 1) {
			return;
		}

		// Line indentation (e.g. before a leading `=>` on its own line) and trailing
		// whitespace before a newline are not alignment.
		if ( $tokens[$stackPtr]['column'] === 1 || str_contains($tokens[$stackPtr]['content'], "\n")) {
			return;
		}

		$nextNonWhitespacePtr = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true, null, false);
		
		if ( $nextNonWhitespacePtr !== false && $this->isTokenAnAssignment($tokens[$nextNonWhitespacePtr])) {
			$error = 'Assignment alignment is not allowed';
			$shouldFix = $phpcsFile->addFixableWarning($error, $stackPtr, 'Aligned');
			
			if ( $shouldFix) {
				$this->fixTokens($phpcsFile, $stackPtr);
			}
		}
	}

	private function isTokenAnAssignment(array $token): bool {
		// `=`, `=>` and compound assignments (`.=`, `+=`, `??=`, ...).
		return isset(Tokens::ASSIGNMENT_TOKENS[$token['code']]);
	}

	private function fixTokens(File $phpcsFile, int $stackPtr): void {
		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($stackPtr, ' ');
		$phpcsFile->fixer->endChangeset();
	}
}
