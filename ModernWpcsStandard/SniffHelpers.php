<?php

declare( strict_types=1 );

namespace ModernWpcsStandard;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

final class SniffHelpers {
	/**
	 * Whether the token is the name of a global function call (not a method call,
	 * static call, instantiation, or declaration).
	 */
	public static function isFunctionCall(File $phpcsFile, int $stackPtr): bool {
		$tokens = $phpcsFile->getTokens();
		$nextPtr = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $stackPtr + 1, null, true);
		if ( $nextPtr === false || $tokens[$nextPtr]['code'] !== T_OPEN_PARENTHESIS) {
			return false;
		}
		$prevPtr = $phpcsFile->findPrevious(Tokens::EMPTY_TOKENS, $stackPtr - 1, null, true);
		if ( $prevPtr === false) {
			return true;
		}
		// Return-by-reference declaration: `function &name()`.
		if ( $tokens[$prevPtr]['code'] === T_BITWISE_AND) {
			$beforeAmpPtr = $phpcsFile->findPrevious(Tokens::EMPTY_TOKENS, $prevPtr - 1, null, true);
			if ( $beforeAmpPtr !== false && $tokens[$beforeAmpPtr]['code'] === T_FUNCTION) {
				return false;
			}
		}
		$notCallPrefixes = [
			T_OBJECT_OPERATOR,
			T_NULLSAFE_OBJECT_OPERATOR,
			T_DOUBLE_COLON,
			T_NEW,
			T_FUNCTION,
			T_CONST,
			T_NS_SEPARATOR,
		];
		return ! in_array($tokens[$prevPtr]['code'], $notCallPrefixes, true);
	}

	/**
	 * Lowercased, unqualified name of a possible global function call token, or null
	 * when the token cannot name a global function (only T_STRING and
	 * T_NAME_FULLY_QUALIFIED qualify; `Foo\bar` / `namespace\bar` are other functions).
	 */
	public static function getGlobalFunctionCallName(File $phpcsFile, int $stackPtr): ?string {
		$token = $phpcsFile->getTokens()[$stackPtr];
		if ( $token['code'] !== T_STRING && $token['code'] !== T_NAME_FULLY_QUALIFIED) {
			return null;
		}
		return strtolower(ltrim($token['content'], '\\'));
	}
}
