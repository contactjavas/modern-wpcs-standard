<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Globals;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowGlobalFunctionsSniff implements Sniff {
	public function register(): array {
		return [T_FUNCTION];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$tokens = $phpcsFile->getTokens();
		$currentToken = $tokens[$stackPtr];
		$namespaceTokenPtr = $phpcsFile->findPrevious( T_NAMESPACE, $stackPtr );
		
		if ( ! empty( $currentToken['conditions'] ) ) {
			return;
		}
		
		if ( $namespaceTokenPtr ) {
			return;
		}
		
		$error = 'Global functions are not allowed';
		$phpcsFile->addError( $error, $stackPtr, 'GlobalFunctions' );
	}
}
