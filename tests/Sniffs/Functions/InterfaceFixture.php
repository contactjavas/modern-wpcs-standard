<?php

declare(strict_types=1); # -*- coding: utf-8 -*-

namespace ModernWpcsStandard\Tests\Sniffs\Functions;

use Brain\Nonces\NonceInterface;

interface InterfaceFixture {


	/**
	 * @param array $request_data
	 *
	 * @return bool
	 */
	public function isAllowed(array $request_data = []): bool;

	/**
	 * @return mixed
	 */
	public function nonce(): mixed;

	/**
	 * @return string
	 */
	public function cap(): string;
}
