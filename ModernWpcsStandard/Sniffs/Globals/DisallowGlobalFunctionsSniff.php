<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Globals;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class DisallowGlobalFunctionsSniff implements Sniff {
	private const NON_GLOBAL_SCOPES = [
		T_CLASS,
		T_INTERFACE,
		T_TRAIT,
		T_ENUM,
		T_ANON_CLASS,
		T_FUNCTION,
		T_CLOSURE,
	];

	public function register(): array {
		return [T_FUNCTION];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();
		$conditions = $tokens[$stackPtr]['conditions'];

		// Cheap check first: methods and nested functions are never global.
		foreach ($conditions as $conditionCode) {
			if (in_array($conditionCode, self::NON_GLOBAL_SCOPES, true)) {
				return;
			}
		}

		if ($this->isNamespaced($phpcsFile, $stackPtr)) {
			return;
		}

		$error = 'Global functions are not allowed';
		$phpcsFile->addError($error, $stackPtr, 'GlobalFunctions');
	}

	private function isNamespaced(File $phpcsFile, int $stackPtr): bool {
		$tokens = $phpcsFile->getTokens();

		// Inside a braced `namespace X { }` block: an empty name is the global block.
		foreach ($tokens[$stackPtr]['conditions'] as $conditionPtr => $conditionCode) {
			if ($conditionCode === T_NAMESPACE) {
				return $this->hasNamespaceName($phpcsFile, $conditionPtr);
			}
		}

		// Otherwise the nearest preceding unscoped `namespace X;` declaration applies.
		$namespacePtr = $stackPtr;
		while (($namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $namespacePtr - 1)) !== false) {
			if (isset($tokens[$namespacePtr]['scope_opener'])) {
				// A braced namespace that ended before this function.
				continue;
			}
			if ($this->hasNamespaceName($phpcsFile, $namespacePtr)) {
				return true;
			}
		}

		return false;
	}

	private function hasNamespaceName(File $phpcsFile, int $namespacePtr): bool {
		$tokens = $phpcsFile->getTokens();
		$nextPtr = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $namespacePtr + 1, null, true);
		if ($nextPtr === false) {
			return false;
		}
		return in_array($tokens[$nextPtr]['code'], [T_STRING, T_NAME_QUALIFIED], true);
	}
}
