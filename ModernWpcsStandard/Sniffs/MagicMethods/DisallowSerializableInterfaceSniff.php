<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\MagicMethods;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * The Serializable interface is deprecated (PHP 8.1); use __serialize() / __unserialize() instead.
 */
class DisallowSerializableInterfaceSniff implements Sniff {
	public function register(): array {
		return [T_CLASS, T_ANON_CLASS];
	}

	public function process(File $phpcsFile, mixed $stackPtr): void {
		$interfaceNames = $phpcsFile->findImplementedInterfaceNames($stackPtr);

		if ( $interfaceNames === false) {
			return;
		}

		foreach ( $interfaceNames as $interfaceName) {
			if ( strtolower(ltrim($interfaceName, '\\')) === 'serializable') {
				$error = 'The Serializable interface is deprecated; use __serialize() and __unserialize() instead';
				$phpcsFile->addError($error, $stackPtr, 'SerializableInterface');
				return;
			}
		}
	}
}
