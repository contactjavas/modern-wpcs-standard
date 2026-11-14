<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Whitespace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowMultipleNewlinesSniff implements Sniff {
	public function register(): array {
		return [T_WHITESPACE];
	}

	/**
	 * Flags the second (and every further) adjacent blank line.
	 *
	 * A blank line is a whitespace token holding nothing but a newline (optionally preceded by
	 * spaces/tabs) that starts a line. Two adjacent blank lines therefore look like:
	 *
	 *   <token ending in a newline> <newline-only whitespace> <newline-only whitespace>
	 *
	 * The first token may be anything whose content ends in a newline (code line terminator,
	 * `//` comment, open tag). The middle token must be whitespace: tokens like `//` comments
	 * carry their own trailing newline, so `// a\n`, `// b\n`, `\n` is only one blank line.
	 */
	public function process(File $phpcsFile, mixed $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		if ( $stackPtr < 2) {
			return;
		}

		if ( $this->isNewlineOnlyWhitespace($tokens[$stackPtr]) === false) {
			return;
		}

		if ( $this->isNewlineOnlyWhitespace($tokens[$stackPtr - 1]) === false) {
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

	private function isNewlineOnlyWhitespace(array $token): bool {
		if ( $token['code'] !== T_WHITESPACE) {
			return false;
		}

		$content = trim($token['content'], " \t");

		return $content === "\n" || $content === "\r\n";
	}

	private function fixTokens(File $phpcsFile, int $stackPtr): void {
		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($stackPtr, '');
		$phpcsFile->fixer->endChangeset();
	}
}
