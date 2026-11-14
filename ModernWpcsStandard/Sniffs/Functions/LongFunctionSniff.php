<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class LongFunctionSniff implements Sniff {
	public $maxFunctionLines = 40;

	public function register(): array {
		return [T_FUNCTION];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		if ( !isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer'])) {
			return;
		}

		$opener = $tokens[$stackPtr]['scope_opener'];
		$closer = $tokens[$stackPtr]['scope_closer'];

		// Count distinct lines inside the body that hold something other than
		// whitespace or comments. Line numbers come from the tokenizer, so this is
		// independent of the file's EOL style (LF / CRLF).
		$codeLines = [];

		for ( $index = $opener + 1; $index < $closer; $index++) {
			if ( isset(Tokens::EMPTY_TOKENS[$tokens[$index]['code']])) {
				continue;
			}

			$codeLines[$tokens[$index]['line']] = true;
		}

		if ( count($codeLines) > $this->maxFunctionLines) {
			$error = "Function is longer than {$this->maxFunctionLines} lines";
			$phpcsFile->addWarning($error, $stackPtr, 'LongFunction');
		}
	}
}
