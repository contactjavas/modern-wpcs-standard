<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Extract;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowExtractSniff implements Sniff {
	public function register(): array {
		return [T_STRING];
	}

	public function process( File $phpcsFile, mixed $stackPtr ): void {
		$tokens = $phpcsFile->getTokens();
		$functionName = $tokens[$stackPtr]['content'];
		$helper = new SniffHelpers();

		if ( $functionName === 'extract' && $helper->isFunctionCall( $phpcsFile, $stackPtr ) ) {
			$error = 'Extract is not allowed';
			$phpcsFile->addError( $error, $stackPtr, 'Extract' );
		}
	}
}
