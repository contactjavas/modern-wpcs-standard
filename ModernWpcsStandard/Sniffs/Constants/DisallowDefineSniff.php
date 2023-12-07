<?php

namespace ModernWpcsStandard\Sniffs\Constants;

use ModernWpcsStandard\SniffHelpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowDefineSniff implements Sniff {
	public function register() {
		return [T_STRING];
	}

	public function process(File $phpcsFile, $stackPtr) {
		$helper = new SniffHelpers();
		if (! $helper->isFunctionCall($phpcsFile, $stackPtr)) {
			return;
		}
		$tokens = $phpcsFile->getTokens();
		$functionName = $tokens[$stackPtr]['content'];
		if ($functionName === 'define') {
			$error = 'Define is not allowed';
			$phpcsFile->addError($error, $stackPtr, 'Define');
		}
	}
}