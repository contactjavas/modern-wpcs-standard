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
