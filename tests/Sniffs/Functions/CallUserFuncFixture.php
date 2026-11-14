<?php

declare( strict_types=1 );

function doSomething(string $arg1, string $arg2, string $arg3) {
}

// Next line should report call_user_func is not allowed
call_user_func('doSomething', 'foo', 'bar', 'baz');
$args = ['foo', 'bar', 'baz'];
// Next line should report call_user_func is not allowed
call_user_func_array('doSomething', $args);
doSomething('foo', 'bar', 'baz');
doSomething(...$args);

\call_user_func('doSomething', 'foo', 'bar', 'baz');
CALL_USER_FUNC_ARRAY('doSomething', $args);
Call_User_Func ('doSomething', 'foo', 'bar', 'baz');
$o->call_user_func('doSomething');
$o?->call_user_func_array('doSomething', $args);
Foo::call_user_func('doSomething');
new Call_User_Func('doSomething');
Foo\call_user_func('doSomething');

class CallUserFuncHolder {
	public function call_user_func() {
	}
}
