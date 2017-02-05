<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MethodsTest extends TestCase
{

	public function testWillDumpArray(): void
	{
		ob_start();
		require __DIR__ . '/dump-convert-to-string.php';
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- require() in dump-convert-to-string.php:12 ----o
string(3) "Bar"
o----------------------------------------------------o

EXPECTED;
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillShowMemory(): void
	{
		ob_start();
		dmem();
		$actual = ob_get_clean();
		$pattern = '/\d+(\.\d+)?M of .* \(MethodsTest->testWillShowMemory\(\) in MethodsTest.php:28\)/';
		self::assertRegExp($pattern, self::normalizeLineEndings($actual));
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
