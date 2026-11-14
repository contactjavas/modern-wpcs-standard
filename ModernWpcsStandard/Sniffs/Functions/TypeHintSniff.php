<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class TypeHintSniff implements Sniff {
	/**
	 * Tokens whose bodies form their own return scope and must be skipped
	 * when scanning a function body for returns/yields.
	 */
	private const NESTED_SCOPES = [
		T_CLOSURE    => true,
		T_ANON_CLASS => true,
		T_FUNCTION   => true,
		T_FN         => true,
	];

	public function register(): array {
		return [T_FUNCTION, T_CLOSURE];
	}

	public function process(File $phpcsFile, mixed $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// Live coding / parse error.
		if ( ! isset($tokens[$stackPtr]['parenthesis_closer'])) {
			return;
		}

		$this->checkForMissingArgumentHints($phpcsFile, $stackPtr);
		$this->checkForMissingReturnHints($phpcsFile, $stackPtr);
	}

	private function checkForMissingArgumentHints(File $phpcsFile, int $stackPtr): void {
		foreach ( $phpcsFile->getMethodParameters($stackPtr) as $param) {
			if ( $param['type_hint'] === '') {
				$phpcsFile->addWarning('Argument type is missing', $stackPtr, 'NoArgumentType');
			}
		}
	}

	private function checkForMissingReturnHints(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();
		$properties = $phpcsFile->getMethodProperties($stackPtr);

		if ( ! $properties['has_body'] || ! isset($tokens[$stackPtr]['scope_closer'])) {
			return;
		}

		$returnTypePtr = $properties['return_type_token'];

		if ( $returnTypePtr !== false) {
			$this->checkReturnTypeSpacing($phpcsFile, $stackPtr, $properties);
		}

		[$nonVoidReturnCount, $voidReturnCount] = $this->countReturns($phpcsFile, $stackPtr);

		$returnType = strtolower(ltrim($properties['return_type'], '?'));
		$hasVoidReturnType = $returnTypePtr !== false && $returnType === 'void';
		$hasNeverReturnType = $returnTypePtr !== false && $returnType === 'never';
		$hasNonVoidReturnType = $returnTypePtr !== false && ! $hasVoidReturnType && ! $hasNeverReturnType;
		$hasNoReturnType = $returnTypePtr === false;

		if ( $hasNonVoidReturnType
			&& ( $nonVoidReturnCount === 0 || $voidReturnCount > 0 )
		) {
			$errorMessage = $voidReturnCount > 0
				? 'Return type with void return'
				: 'Return type with no return';

			$errorType = $voidReturnCount > 0
				? 'IncorrectVoidReturn'
				: 'UnusedReturnType';

			$phpcsFile->addError($errorMessage, $stackPtr, $errorType);
		}

		if ( $hasNoReturnType && $nonVoidReturnCount > 0) {
			$phpcsFile->addWarning('Return type is missing', $stackPtr, 'NoReturnType');
		}

		if ( $hasVoidReturnType && $nonVoidReturnCount > 0) {
			$phpcsFile->addError('Void return type when returning non-void', $stackPtr, 'IncorrectVoidReturnType');
		}
	}

	/**
	 * @param array<string, mixed> $properties Result of File::getMethodProperties().
	 */
	private function checkReturnTypeSpacing(File $phpcsFile, int $stackPtr, array $properties): void {
		$tokens = $phpcsFile->getTokens();
		$colonPtr = $phpcsFile->findPrevious(
			T_COLON,
			$properties['return_type_token'] - 1,
			$tokens[$stackPtr]['parenthesis_closer']
		);

		if ( $colonPtr === false) {
			return;
		}

		if ( $tokens[$colonPtr - 1]['code'] !== T_CLOSE_PARENTHESIS) {
			$phpcsFile->addError(
				'Return type colon should be right after closing function parenthesis',
				$colonPtr,
				'ExtraSpace'
			);
		}

		if ( $tokens[$colonPtr + 1]['code'] !== T_WHITESPACE) {
			$phpcsFile->addError('Missing space before return type', $colonPtr, 'MissingSpace');
		}

		if ( $tokens[$properties['return_type_end_token'] + 1]['code'] !== T_WHITESPACE) {
			$phpcsFile->addError('Missing space after return type', $colonPtr, 'MissingSpace');
		}
	}

	/**
	 * Counts returns/yields in the function's own scope (nested closures,
	 * arrow functions, functions and anonymous classes are skipped).
	 *
	 * A generator (any `yield` / `yield from`) counts as having a non-void
	 * return, and its bare `return;` statements are not counted as void returns.
	 *
	 * @return array{int, int} [nonVoidReturnCount, voidReturnCount]
	 */
	private function countReturns(File $phpcsFile, int $stackPtr): array {
		$tokens = $phpcsFile->getTokens();
		$nonVoidReturnCount = 0;
		$voidReturnCount = 0;
		$isGenerator = false;
		$end = $tokens[$stackPtr]['scope_closer'];

		for ( $ptr = $tokens[$stackPtr]['scope_opener'] + 1; $ptr < $end; $ptr++) {
			$code = $tokens[$ptr]['code'];

			if ( isset(self::NESTED_SCOPES[$code])) {
				if ( isset($tokens[$ptr]['scope_closer'])) {
					$ptr = $tokens[$ptr]['scope_closer'];
				}
				continue;
			}

			if ( $code === T_YIELD || $code === T_YIELD_FROM) {
				$isGenerator = true;
				$nonVoidReturnCount++;
				continue;
			}

			if ( $code === T_RETURN) {
				$this->isBareReturn($phpcsFile, $ptr) ? $voidReturnCount++ : $nonVoidReturnCount++;
			}
		}

		if ( $isGenerator) {
			$voidReturnCount = 0;
		}

		return [$nonVoidReturnCount, $voidReturnCount];
	}

	private function isBareReturn(File $phpcsFile, int $returnPtr): bool {
		$tokens = $phpcsFile->getTokens();
		$nextPtr = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $returnPtr + 1, null, true);

		return $nextPtr === false
			|| $tokens[$nextPtr]['code'] === T_SEMICOLON
			|| $tokens[$nextPtr]['code'] === T_CLOSE_TAG;
	}
}
