<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Constants;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowDefineSniff implements Sniff {
	public function register(): array {
		return [T_STRING, T_NAME_FULLY_QUALIFIED];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		if ( SniffHelpers::getGlobalFunctionCallName($phpcsFile, $stackPtr) === 'define'
			&& SniffHelpers::isFunctionCall($phpcsFile, $stackPtr)
		) {
			$error = 'Define is not allowed';
			$phpcsFile->addError($error, $stackPtr, 'Define');
		}
	}
}
