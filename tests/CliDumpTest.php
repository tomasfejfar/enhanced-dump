<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class CliDumpTest extends TestCase
{
	public function testWillDumpArray(): void
	{
		$test = [];
		ob_start();
		d($test);
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- CliDumpTest->testWillDumpArray() in CliDumpTest.php:13 ----o
array(0) {
}
o----------------------------------------------------------------o

EXPECTED;
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillReportCorrectFile(): void
	{
		require_once __DIR__ . '/dump-in-class-or-function.php';
		ob_start();
		testDump1();
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- testDump1() in dump-in-class-or-function.php:5 ----o
int(5)
o--------------------------------------------------------o

EXPECTED;
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillReportCorrectMethod(): void
	{
		require_once __DIR__ . '/dump-in-class-or-function.php';
		ob_start();
		testDump2();
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- testDump1() in dump-in-class-or-function.php:5 ----o
int(5)
o--------------------------------------------------------o

EXPECTED;
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

o---- EnhancedDumpSampleClass->someMethod() in dump-in-class-or-function.php:16 ----o
string(4) "DUMP"
o-----------------------------------------------------------------------------------o

EXPECTED;
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillWorkInDumpingWithoutFunctionOrClassAround(): void
	{
		ob_start();
		require __DIR__ . '/dump-directly-in-file.php';
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- require() in dump-directly-in-file.php:3 ----o
string(4) "DUMP"
o--------------------------------------------------o

EXPECTED;
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillWorkInPlainPhp(): void
	{
		$script = __DIR__ . '/dump-directly-in-file.php';
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

o---- directly in dump-directly-in-file.php:3 ----o
string(4) "DUMP"
o-------------------------------------------------o

EXPECTED;
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
