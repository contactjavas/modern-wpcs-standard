<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\MagicMethods;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowMagicSetSniff implements Sniff {
	public function register(): array {
		return [T_FUNCTION];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$functionName = $phpcsFile->getDeclarationName( $stackPtr );
		
		if ( $functionName === '__set' ) {
			$error = 'Magic setters are not allowed';
			$phpcsFile->addError( $error, $stackPtr, 'MagicSet' );
		}
	}
}
