<?php
class Foo {
	public $x = 10;

	public function __toString()
	{
		return 'Bar';
	}

}
$x = new Foo();
ds($x);
