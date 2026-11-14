<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Functions;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowCallUserFuncSniff implements Sniff {
	private const DISALLOWED_FUNCTIONS = [
		'call_user_func',
		'call_user_func_array',
	];

	public function register(): array {
		return [T_STRING, T_NAME_FULLY_QUALIFIED];
	}

	public function process(File $phpcsFile, mixed $stackPtr): void {
		$helper = new SniffHelpers();
		$functionName = $helper->getGlobalFunctionCallName($phpcsFile, $stackPtr);

		if ( in_array($functionName, self::DISALLOWED_FUNCTIONS, true)
			&& $helper->isFunctionCall($phpcsFile, $stackPtr)
		) {
			$error = 'call_user_func and call_user_func_array are not allowed';
			$phpcsFile->addError($error, $stackPtr, 'CallUserFunc');
		}
	}
}
