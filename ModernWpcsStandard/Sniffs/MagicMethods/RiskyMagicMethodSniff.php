<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\MagicMethods;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class RiskyMagicMethodSniff implements Sniff {
	public function register(): array {
		return [T_FUNCTION];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$functionName = $phpcsFile->getDeclarationName( $stackPtr );
		$riskyMagicMethods = [
			'__invoke',
			'__call',
			'__callStatic',
		];
		
		if ( in_array( $functionName, $riskyMagicMethods ) ) {
			$error = 'Magic methods are discouraged';
			$phpcsFile->addWarning( $error, $stackPtr, 'RiskyMagicMethod' );
		}
	}
}
