<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Extract;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowExtractSniff implements Sniff {
	public function register(): array {
		return [T_STRING, T_NAME_FULLY_QUALIFIED];
	}

	public function process(File $phpcsFile, mixed $stackPtr): void {
		$helper = new SniffHelpers();

		if ( $helper->getGlobalFunctionCallName($phpcsFile, $stackPtr) === 'extract'
			&& $helper->isFunctionCall($phpcsFile, $stackPtr)
		) {
			$error = 'Extract is not allowed';
			$phpcsFile->addError($error, $stackPtr, 'Extract');
		}
	}
}
