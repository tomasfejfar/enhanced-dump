<?php

function testDump1() {
	$variable = 5;
	d($variable);
}

function testDump2() {
	testDump1();
}

class EnhancedDumpSampleClass{

	public function someMethod()
	{
		d('DUMP');
	}
}
