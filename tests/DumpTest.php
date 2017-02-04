<?php

declare(strict_types=1);

class DumpTest extends \PHPUnit\Framework\TestCase
{
	public function testWillDumpArray(): void
	{
		$test = [];
		ob_start();
		d($test);
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- DumpTest->testWillDumpArray() line 11 (in DumpTest.php) ----o
array(0) {
}
o-----------------------------------------------------------------o

EXPECTED;
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillReportCorrectFile(): void
	{
		require_once __DIR__ . '/testdumpfile.php';
		ob_start();
		testDump1();
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- testDump1() line 5 (in testdumpfile.php) ----o
int(5)
o--------------------------------------------------o

EXPECTED;
		self::assertSame($expected, self::normalizeLineEndings($actual));
	}

	public function testWillReportCorrectMethod(): void
	{
		require_once __DIR__ . '/testdumpfile.php';
		ob_start();
		testDump2();
		$actual = ob_get_clean();
		$expected = <<<EXPECTED

o---- testDump1() line 5 (in testdumpfile.php) ----o
int(5)
o--------------------------------------------------o

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
