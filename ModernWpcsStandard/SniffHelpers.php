<?php

declare( strict_types=1 );

namespace ModernWpcsStandard;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class SniffHelpers {
	/**
	 * Whether the token is the name of a global function call (not a method call,
	 * static call, instantiation, or declaration).
	 */
	public function isFunctionCall(File $phpcsFile, int $stackPtr): bool {
		$tokens = $phpcsFile->getTokens();
		$nextPtr = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $stackPtr + 1, null, true);
		if ( $nextPtr === false || $tokens[$nextPtr]['code'] !== T_OPEN_PARENTHESIS) {
			return false;
		}
		$prevPtr = $phpcsFile->findPrevious(Tokens::EMPTY_TOKENS, $stackPtr - 1, null, true);
		if ( $prevPtr === false) {
			return true;
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
	public function getGlobalFunctionCallName(File $phpcsFile, int $stackPtr): ?string {
		$token = $phpcsFile->getTokens()[$stackPtr];
		if ( $token['code'] !== T_STRING && $token['code'] !== T_NAME_FULLY_QUALIFIED) {
			return null;
		}
		return strtolower(ltrim($token['content'], '\\'));
	}

	// From https://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string
	public function doesStringEndWith(string $string, string $test): bool {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if ( $testlen > $strlen) {
			return false;
		}
		return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
	}

	public function getNextNonWhitespace(File $phpcsFile, int $stackPtr): ?array {
		$tokens = $phpcsFile->getTokens();
		$nextNonWhitespacePtr = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true, null, true);
		return $nextNonWhitespacePtr ? $tokens[$nextNonWhitespacePtr] : null;
	}

	public function getNextNewlinePtr(File $phpcsFile, int $stackPtr): int|bool {
		$tokens = $phpcsFile->getTokens();
		$ptr = $stackPtr;
		while ( ( $ptr = $phpcsFile->findNext(T_WHITESPACE, $ptr + 1) ) !== false) {
			if ( str_contains($tokens[$ptr]['content'], "\n")) {
				return $ptr;
			}
		}
		return false;
	}

	public function getArgumentTypePtr(File $phpcsFile, int $stackPtr): int|bool {
		$ignoredTypes = [
			T_WHITESPACE,
			T_ELLIPSIS,
		];

		$openParenPtr = $phpcsFile->findPrevious(T_OPEN_PARENTHESIS, $stackPtr - 1, null, false);

		if ( ! $openParenPtr) {
			return false;
		}

		return $phpcsFile->findPrevious($ignoredTypes, $stackPtr - 1, $openParenPtr, true);
	}

	public function isReturnValueVoid(File $phpcsFile, int $stackPtr): bool {
		$tokens = $phpcsFile->getTokens();

		if ( ! in_array($tokens[$stackPtr]['code'], [T_RETURN, T_YIELD], false)) {
			return false;
		}
		
		$returnValue = $this->getNextNonWhitespace($phpcsFile, $stackPtr);
		
		return ! $returnValue || $returnValue['code'] === 'PHPCS_T_SEMICOLON';
	}

	public function getNextReturnTypePtr(File $phpcsFile, int $stackPtr): int|bool {
		$startOfFunctionPtr = $this->getStartOfFunctionPtr($phpcsFile, $stackPtr);
		$colonPtr = $phpcsFile->findNext(T_COLON, $stackPtr, $startOfFunctionPtr);
		
		if ( ! $colonPtr) {
			return false;
		}

		$endOfTypePtr = $phpcsFile->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $colonPtr + 1);

		if ( ! $endOfTypePtr) {
			throw new \Exception('Found colon for return type but no end-of-line');
		}

		return $phpcsFile->findPrevious([T_WHITESPACE], $endOfTypePtr - 1, $colonPtr, true);
	}

	public function getNextSemicolonPtr(File $phpcsFile, int $stackPtr): int|bool {
		return $phpcsFile->findNext(T_SEMICOLON, $stackPtr + 1);
	}

	public function getEndOfFunctionPtr(File $phpcsFile, int $stackPtr): int|bool {
		$tokens = $phpcsFile->getTokens();
		if ( $this->isFunctionJustSignature($phpcsFile, $stackPtr)) {
			return $this->getNextSemicolonPtr($phpcsFile, $stackPtr);
		}
		$openFunctionBracketPtr = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr + 1);
		return $openFunctionBracketPtr && isset($tokens[$openFunctionBracketPtr]['bracket_closer'])
			? $tokens[$openFunctionBracketPtr]['bracket_closer']
			: $this->getNextSemicolonPtr($phpcsFile, $stackPtr);
	}

	public function getStartOfFunctionPtr(File $phpcsFile, int $stackPtr): int|bool {
		$openFunctionBracketPtr = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr + 1);
		$nextSemicolonPtr = $this->getNextSemicolonPtr($phpcsFile, $stackPtr);
		
		if ( $openFunctionBracketPtr && $nextSemicolonPtr && $openFunctionBracketPtr > $nextSemicolonPtr) {
			return $nextSemicolonPtr;
		}

		return $openFunctionBracketPtr
			? $openFunctionBracketPtr + 1
			: $this->getEndOfFunctionPtr($phpcsFile, $stackPtr);
	}

	public function isFunctionJustSignature(File $phpcsFile, int $stackPtr): bool {
		$openFunctionBracketPtr = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr + 1);
		$nextSemicolonPtr = $this->getNextSemicolonPtr($phpcsFile, $stackPtr);

		if ( $openFunctionBracketPtr && $nextSemicolonPtr && $openFunctionBracketPtr > $nextSemicolonPtr) {
			return true;
		}
		
		return ! $openFunctionBracketPtr;
	}
}
