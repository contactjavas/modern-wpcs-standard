<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Arrays;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowLongformArraySniff implements Sniff {
	public function register(): array {
		return [T_ARRAY];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$tokens = $phpcsFile->getTokens();
		$functionName = $tokens[$stackPtr]['content'];
		$helper = new SniffHelpers();

		if ( $functionName === 'array' && $helper->isFunctionCall( $phpcsFile, $stackPtr ) ) {
			$error = 'Longform array is not allowed';
			$shouldFix = $phpcsFile->addFixableError( $error, $stackPtr, 'LongformArray' );
			
			if ( $shouldFix ) {
				$this->fixTokens( $phpcsFile, $stackPtr );
			}
		}
	}

	private function fixTokens( File $phpcsFile, mixed $stackPtr ): void {
		$tokens = $phpcsFile->getTokens();
		$openParenPtr = $tokens[$stackPtr]['parenthesis_opener'];
		$closeParenPtr = $tokens[$stackPtr]['parenthesis_closer'];
		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken( $stackPtr, '' );
		$phpcsFile->fixer->replaceToken( $openParenPtr, '[' );
		$phpcsFile->fixer->replaceToken( $closeParenPtr, ']' );
		$phpcsFile->fixer->endChangeset();
	}
}
