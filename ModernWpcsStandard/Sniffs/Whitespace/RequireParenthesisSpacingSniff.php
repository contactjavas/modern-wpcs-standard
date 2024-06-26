<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Whitespace;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class RequireParenthesisSpacingSniff implements Sniff {
	public function register(): array {
		return [T_OPEN_PARENTHESIS, T_CLOSE_PARENTHESIS];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$stackPtr];
		$isBefore = ( $token['type'] === 'T_OPEN_PARENTHESIS' );
		$nextTokenPtr = $isBefore ? $stackPtr + 1 : $stackPtr - 1;
		$nextToken = $tokens[$nextTokenPtr];
		
		if ( ! isset( $nextToken['type'] ) ) {
			return;
		}
		
		$allowedTypes = ['T_WHITESPACE'];
		$allowedTypes[] = $isBefore ? 'T_CLOSE_PARENTHESIS' : 'T_OPEN_PARENTHESIS';
		
		if ( in_array( $nextToken['type'], $allowedTypes, true ) ) {
			return;
		}
		
		$error = 'Parenthesis content must be padded by a space';
		$shouldFix = $phpcsFile->addFixableError( $error, $stackPtr, 'Missing' );
		
		if ( $shouldFix ) {
			$this->fixTokens( $phpcsFile, $nextTokenPtr, $isBefore );
		}
	}

	private function fixTokens( File $phpcsFile, int $stackPtr, bool $isBefore ): void {
		$phpcsFile->fixer->beginChangeset();
		
		if ( $isBefore ) {
			$phpcsFile->fixer->addContentBefore( $stackPtr, ' ' );
		} else {
			$phpcsFile->fixer->addContent( $stackPtr, ' ' );
		}
		
		$phpcsFile->fixer->endChangeset();
	}
}
