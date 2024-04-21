<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\MagicMethods;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowMagicSerializeSniff implements Sniff {
	public function register(): array {
		return [T_FUNCTION];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$functionName = $phpcsFile->getDeclarationName( $stackPtr );

		if ( $functionName === '__serialize' ) {
			$error = 'Magic serialize is not allowed';
			$phpcsFile->addError( $error, $stackPtr, 'MagicSerialize' );
		}
	}
}
