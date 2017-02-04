<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Class WebDumpTest
 *
 * @is
 */
class WebDumpTest extends TestCase
{

	public function setUp()
	{
		if (!defined('ENHANCED_DUMP_FORCED_SAPI')) {
			define('ENHANCED_DUMP_FORCED_SAPI', 'fpm-fcgi');
		}
	}

	public function testWillDumpArray(): void
	{
		$test = [];
		ob_start();
		d($test);
		$actual = ob_get_clean();
		$expected = <<<EXPECTED
<div style="background:#f8f8f8;margin:5px;padding:5px;border: dashed grey 1px;font-family: monospace;font-weight: bold;">

WebDumpTest->testWillDumpArray() <small>in <a href="editor://**DIR**WebDumpTest.php">WebDumpTest.php</a>:26</small>
<pre style="margin:0px;padding:0px;font-weight: normal;">
array(0) {
}
</pre>
</div>

EXPECTED;
		$expected = str_replace('**DIR**', __DIR__ . DIRECTORY_SEPARATOR, $expected);
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillReportCorrectFile(): void
	{
		require_once __DIR__ . '/dump-in-class-or-function.php';
		ob_start();
		testDump1();
		$actual = ob_get_clean();
		$expected = <<<EXPECTED
<div style="background:#f8f8f8;margin:5px;padding:5px;border: dashed grey 1px;font-family: monospace;font-weight: bold;">

testDump1() <small>in <a href="editor://**DIR**dump-in-class-or-function.php">dump-in-class-or-function.php</a>:5</small>
<pre style="margin:0px;padding:0px;font-weight: normal;">
int(5)
</pre>
</div>

EXPECTED;
		$expected = str_replace('**DIR**', __DIR__ . DIRECTORY_SEPARATOR, $expected);
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillReportCorrectMethod(): void
	{
		require_once __DIR__ . '/dump-in-class-or-function.php';
		ob_start();
		testDump2();
		$actual = ob_get_clean();
		$expected = <<<EXPECTED
<div style="background:#f8f8f8;margin:5px;padding:5px;border: dashed grey 1px;font-family: monospace;font-weight: bold;">

testDump1() <small>in <a href="editor://**DIR**dump-in-class-or-function.php">dump-in-class-or-function.php</a>:5</small>
<pre style="margin:0px;padding:0px;font-weight: normal;">
int(5)
</pre>
</div>

EXPECTED;
		$expected = str_replace('**DIR**', __DIR__ . DIRECTORY_SEPARATOR, $expected);
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillReportCorrectClass(): void
	{
		require_once __DIR__ . '/dump-in-class-or-function.php';
		$obj = new EnhancedDumpSampleClass();
		ob_start();
		$obj->someMethod();
		$actual = ob_get_clean();
		$expected = <<<EXPECTED
<div style="background:#f8f8f8;margin:5px;padding:5px;border: dashed grey 1px;font-family: monospace;font-weight: bold;">

EnhancedDumpSampleClass->someMethod() <small>in <a href="editor://**DIR**dump-in-class-or-function.php">dump-in-class-or-function.php</a>:16</small>
<pre style="margin:0px;padding:0px;font-weight: normal;">
string(4) "DUMP"
</pre>
</div>

EXPECTED;
		$expected = str_replace('**DIR**', __DIR__ . DIRECTORY_SEPARATOR, $expected);
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillWorkInDumpingWithoutFunctionOrClassAround(): void
	{
		ob_start();
		require __DIR__ . '/dump-directly-in-file.php';
		$actual = ob_get_clean();
		$expected = <<<EXPECTED
<div style="background:#f8f8f8;margin:5px;padding:5px;border: dashed grey 1px;font-family: monospace;font-weight: bold;">

require() <small>in <a href="editor://**DIR**dump-directly-in-file.php">dump-directly-in-file.php</a>:3</small>
<pre style="margin:0px;padding:0px;font-weight: normal;">
string(4) "DUMP"
</pre>
</div>

EXPECTED;
		$expected = str_replace('**DIR**', __DIR__ . DIRECTORY_SEPARATOR, $expected);
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillWorkInPlainPhp(): void
	{
		$script = __DIR__ . '/dump-directly-in-file-force-web.php';
		$process = new \Symfony\Component\Process\Process('php ' . $script);
		$process->start();
		do {
			$process->checkTimeout();
		} while ($process->isRunning() && (usleep(1000) !== false));
		if (!$process->isSuccessful()) {
			throw new \Exception($process->getErrorOutput());
		}
		$actual = $process->getOutput();
		$expected = <<<EXPECTED
<div style="background:#f8f8f8;margin:5px;padding:5px;border: dashed grey 1px;font-family: monospace;font-weight: bold;">

directly in <a href="editor://**DIR**dump-directly-in-file-force-web.php">dump-directly-in-file-force-web.php</a>:4
<pre style="margin:0px;padding:0px;font-weight: normal;">
string(4) "DUMP"
</pre>
</div>

EXPECTED;
		$expected = str_replace('**DIR**', __DIR__ . DIRECTORY_SEPARATOR, $expected);
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	/**
	 * Normalizes line ending as the dump uses PHP_EOL but the expected string has linux endings
	 *
	 * @param string $string
	 * @return string
	 */
	public static function normalizeLineEndings(string $string): string
	{
		return str_replace(PHP_EOL, "\n", $string);
	}
}
