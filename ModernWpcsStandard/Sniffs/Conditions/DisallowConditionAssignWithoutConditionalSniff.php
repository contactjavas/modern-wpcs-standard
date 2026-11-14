<?php

declare( strict_types=1 );

namespace ModernWpcsStandard\Sniffs\Conditions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class DisallowConditionAssignWithoutConditionalSniff implements Sniff {
	private const COMPARATORS = [
		T_IS_EQUAL,
		T_IS_NOT_EQUAL,
		T_IS_IDENTICAL,
		T_IS_NOT_IDENTICAL,
		T_IS_SMALLER_OR_EQUAL,
		T_IS_GREATER_OR_EQUAL,
		T_LESS_THAN,
		T_GREATER_THAN,
		T_SPACESHIP,
	];

	// Nested code whose `=` / comparators do not belong to the condition itself.
	private const NESTED_SCOPES = [
		T_CLOSURE,
		T_FN,
		T_ANON_CLASS,
	];

	public function register(): array {
		return [T_IF, T_ELSEIF, T_WHILE];
	}

	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		$openerPtr = $this->getConditionOpener($phpcsFile, $stackPtr);
		if ($openerPtr === null) {
			return;
		}
		$closerPtr = $tokens[$openerPtr]['parenthesis_closer'];

		$hasAssignment = false;
		for ($ptr = $openerPtr + 1; $ptr < $closerPtr; $ptr++) {
			$code = $tokens[$ptr]['code'];
			if (in_array($code, self::NESTED_SCOPES, true) && isset($tokens[$ptr]['scope_closer'])) {
				$ptr = $tokens[$ptr]['scope_closer'];
				continue;
			}
			if (in_array($code, self::COMPARATORS, true)) {
				return;
			}
			if ($code === T_EQUAL) {
				$hasAssignment = true;
			}
		}

		if ( ! $hasAssignment) {
			return;
		}

		$error = 'Conditions that contain assignments must have explicit comparators';
		$phpcsFile->addError($error, $openerPtr, 'ConditionAssignWithoutConditional');
	}

	private function getConditionOpener(File $phpcsFile, int $stackPtr): ?int {
		$tokens = $phpcsFile->getTokens();
		if (isset($tokens[$stackPtr]['parenthesis_opener'])) {
			return $tokens[$stackPtr]['parenthesis_opener'];
		}

		// Fallback, e.g. the `while` of a `do { } while ( ... );` loop.
		$nextPtr = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $stackPtr + 1, null, true);
		if ($nextPtr === false
			|| $tokens[$nextPtr]['code'] !== T_OPEN_PARENTHESIS
			|| ! isset($tokens[$nextPtr]['parenthesis_closer'])
		) {
			return null;
		}
		return $nextPtr;
	}
}
