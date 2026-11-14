<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\MagicMethods;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class RiskyMagicMethodSniff implements Sniff {
	public function register(): array {
		return [T_FUNCTION];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		// PHP method names are case-insensitive.
		$functionName = strtolower((string) $phpcsFile->getDeclarationName($stackPtr));
		$riskyMagicMethods = [
			'__invoke',
			'__call',
			'__callstatic',
		];

		if ( in_array($functionName, $riskyMagicMethods, true)) {
			$error = 'Magic methods are discouraged';
			$phpcsFile->addWarning($error, $stackPtr, 'RiskyMagicMethod');
		}
	}
}
