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
