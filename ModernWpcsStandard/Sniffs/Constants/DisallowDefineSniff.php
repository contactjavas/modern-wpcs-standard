<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Constants;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowDefineSniff implements Sniff {
	public function register(): array {
		return [T_STRING];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$helper = new SniffHelpers();

		if ( ! $helper->isFunctionCall( $phpcsFile, $stackPtr ) ) {
			return;
		}
		
		$tokens = $phpcsFile->getTokens();
		$functionName = $tokens[$stackPtr]['content'];
		
		if ( $functionName === 'define' ) {
			$error = 'Define is not allowed';
			$phpcsFile->addError( $error, $stackPtr, 'Define' );
		}
	}
}
