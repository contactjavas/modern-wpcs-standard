<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\MagicMethods;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowMagicGetSniff implements Sniff {
	public function register(): array {
		return [T_FUNCTION];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$functionName = $phpcsFile->getDeclarationName( $stackPtr );
		
		if ( $functionName === '__get' ) {
			$error = 'Magic getters are not allowed';
			$phpcsFile->addError( $error, $stackPtr, 'MagicGet' );
		}
	}
}
