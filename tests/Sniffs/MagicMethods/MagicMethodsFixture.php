<?php

declare(strict_types=1);

namespace ModernWpcsStandard\Tests\Sniffs\MagicMethods;

class MagicMethodsFixture {
	public function doSomething() {
		$all = ['foo' => 'bar' ];
		$rest = array('x' => 'y');
		extract($all);
		$define = 'foo';
		$define;
		$rest;
	}

	public function getActual() {
		return 'hello';
	}

	// __serialize is allowed (modern replacement for Serializable)
	public function __serialize() {
	}

	// Next line should report no get allowed
	public function __get($var) {
		$var;
	}

	// Next line should report no set allowed
	public function __set($var, $val) {
		$var;
		$val;
	}

	// Next line should report risky method
	public function __invoke() {
	}

	// Next line should report risky method
	public function __call($name, $args) {
		$name;
		$args;
	}

	// Next line should report risky method
	public static function __callStatic($name, $args) {
		$name;
		$args;
	}
}

class MagicMethodsCaseFixture {
	public function __GET($var) {
	}

	public function __Set($var, $val) {
	}

	public function __SERIALIZE() {
	}

	public static function __callstatic($name, $args) {
	}

	public function __INVOKE() {
	}

	public function __get_value() {
	}
}

class SerializableFixture implements \Serializable {
}

class SerializableCaseFixture implements \Countable, serializable {
}

$anon = new class implements \Serializable {
};

class ModernSerializeFixture implements \JsonSerializable {
	public function __unserialize(array $data) {
	}
}
