<?php

namespace EnhancedDump {
	function webOnly(string $text): string
	{
		$sapi = defined('ENHANCED_DUMP_FORCED_SAPI') ? ENHANCED_DUMP_FORCED_SAPI : php_sapi_name();
		if ($sapi === 'cli') {
			return '';
		}
		return $text;
	}

	function cliOnly(string $text): string
	{
		$sapi = defined('ENHANCED_DUMP_FORCED_SAPI') ? ENHANCED_DUMP_FORCED_SAPI : php_sapi_name();
		if ($sapi !== 'cli') {
			return '';
		}
		return $text;
	}

	function dumpHeader(string $trace)
	{
		$result = '';
		$result .= webOnly('<div style="background:#f8f8f8;margin:5px;padding:5px;border: dashed grey 1px;font-family: monospace;font-weight: bold;">' . PHP_EOL);
		$result .= PHP_EOL . cliOnly('o---- ') . $trace . cliOnly(' ----o') . PHP_EOL;
		$result .= webOnly('<pre style="margin:0px;padding:0px;font-weight: normal;">' . PHP_EOL);
		return $result;
	}

	function dumpFooter($trace)
	{
		$result = '';
		$result .= webOnly('</pre>' . PHP_EOL);
		$result .= webOnly('</div>' . PHP_EOL);
		$result .= cliOnly('o-----' . str_repeat('-', strlen($trace)) . '-----o' . PHP_EOL);
		return $result;
	}

	function dtrace($debugBacktrace)
	{
		if (isset($debugBacktrace[1])) {
			$index = 1;
			$trace = $debugBacktrace[$index];
		} else {
			$index = 0;
			$trace = $debugBacktrace[$index];
			$line = $trace['line'];
			$file = basename($trace['file']);
			$filePath = $trace['file'];
			$traceFile = cliOnly('%s') . webOnly('<a href="editor://' . $filePath . '">%s</a>');
			return sprintf('directly in ' . $traceFile . ':%s', $file, $line);
		}
		$line = $debugBacktrace[$index - 1]['line'];
		$file = basename($debugBacktrace[0]['file']);
		$filePath = $debugBacktrace[0]['file'];
		$location = '';
		$type = '';
		if (isset($trace['class'])) {
			$location = $trace['class'];
			$type = $trace['type'];
		}
		$function = isset($trace['function']) ? $trace['function'] : '';
		$traceFile = cliOnly('%5$s') . webOnly('<a href="editor://' . $filePath . '">%5$s</a>');
		$traceString = '%1$s%2$s%3$s() ' . webOnly('<small>') . 'in ' . $traceFile . ':%4$s' . webOnly('</small>');
		return sprintf($traceString, $location, $type, $function, $line, $file);
	}
}

namespace {

	/**
	 * Dump variable
	 *
	 * @param mixed $var The variable to dump.
	 * @param  string $label OPTIONAL Label to prepend to output.
	 */
	function d()
	{
		$result = '';
		$trace = EnhancedDump\dtrace(debug_backtrace());
		$result .= EnhancedDump\dumpHeader($trace);
		ob_start();
		call_user_func_array('var_dump', func_get_args());
		$result .= ob_get_clean();
		$result .= EnhancedDump\dumpFooter($trace);
		echo $result;
	}

	/**
	 * Dump variable and die.
	 *
	 * @param mixed $var The variable to dump.
	 * @param  string $label OPTIONAL Label to prepend to output.
	 */
	function dd()
	{
		$result = '';
		$trace = EnhancedDump\dtrace(debug_backtrace());
		$result .= EnhancedDump\dumpHeader($trace);
		ob_start();
		call_user_func_array('var_dump', func_get_args());
		$result .= ob_get_clean();
		$result .= EnhancedDump\dumpFooter($trace);
		echo $result;
		die();
	}

	/**
	 * Dump variable as string
	 *
	 * @param mixed $var The variable to dump.
	 * @param  string $label OPTIONAL Label to prepend to output.
	 */
	function ds()
	{
		$result = '';
		$trace = EnhancedDump\dtrace(debug_backtrace());
		$result .= EnhancedDump\dumpHeader($trace);

		$args = func_get_args();
		array_walk($args, function (&$item) {
			$item = (string) $item;
		});
		ob_start();
		call_user_func_array('var_dump', $args);
		$result .= ob_get_clean();
		$result .= EnhancedDump\dumpFooter($trace);
		echo $result;
	}

	/**
	 * Dump variable as string and die.
	 *
	 * @param mixed $var The variable to dump.
	 * @param  string $label OPTIONAL Label to prepend to output.
	 */
	function dsd($var, $label = null)
	{
		$result = '';
		$trace = EnhancedDump\dtrace(debug_backtrace());
		$result .= EnhancedDump\dumpHeader($trace);

		$args = func_get_args();
		array_walk($args, function (&$item) {
			$item = (string) $item;
		});
		ob_start();
		call_user_func_array('var_dump', $args);
		$result .= ob_get_clean();
		$result .= EnhancedDump\dumpFooter($trace);
		echo $result;
		die();
	}

	/**
	 * Dump variable as string into autoselectable textarea
	 *
	 * @param mixed $var The variable to dump.
	 */
	function dsql($var)
	{
		if (!headers_sent()) {
			header('Content-Type:text/html; charset=utf-8');
		}
		$result = '';
		$trace = EnhancedDump\dtrace(debug_backtrace());
		$result .= EnhancedDump\dumpHeader($trace);
		$result .= EnhancedDump\webOnly('<textarea style="width:100%;height:100%" onclick="this.select()">' . PHP_EOL);
		ob_start();
		echo (string) $var . PHP_EOL;
		$result .= ob_get_clean();
		$result .= EnhancedDump\webOnly('</textarea>');
		$result .= EnhancedDump\dumpFooter($trace);
		echo $result;
		die();
	}

	/**
	 * Dump memory usage
	 */
	function dmem()
	{
		$result = '';
		$result .= EnhancedDump\webOnly('<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
		$trace = EnhancedDump\dtrace(debug_backtrace());
		$memUsage = memory_get_usage();
		$units = [
			1024 => 'K',
			1024 * 1024 => 'M',
			1024 * 1024 * 1024 => 'G',
		];
		$memory = $memUsage;
		$unit = '';
		foreach ($units as $amount => $unit) {
			if (($memUsage / $amount) < 1024) {
				$precision = (strlen($amount) - 1) / 3 - 1;
				$memory = round($memUsage / $amount, $precision);
				break;
			}
		}
		$result .= sprintf('%s%s of %s (%s)', $memory, $unit, ini_get("memory_limit"), $trace);
		$result .= EnhancedDump\webOnly('</div>' . PHP_EOL);
		echo $result;
	}

	/**
	 * Dump two dimensional array as table
	 *
	 * @param array $data
	 */
	function dtable(array $data)
	{
		if (!$data) {
			d('nothing for dtable()');
		}

		if (!headers_sent()) {
			header('Content-Type:text/html; charset=utf-8');
		}

		if (array_key_exists(0, $data) && is_array($data[0])) {
			//@todo lepší by bylo vybírat max parametrů
			$cols = array_keys($data[0]);
			$pairs = false;
		} else {
			$cols = [
				'Key',
				'Value',
			];
			$pairs = true;
		}

		echo '<table border="1"><tr>';
		echo '<th colspan="' . count($cols) . '"><h2>Items Count: ' . count($data) . '</h2></th>';
		echo '</tr><tr>';
		foreach ($cols as $col) {
			echo '<th>' . $col . '</th>';
		}
		echo '</tr>';
		foreach ($data as $key => $row) {
			echo '<tr>';
			foreach ($cols as $col) {
				if ($pairs) {
					echo '<td>' . $key . '</td>';
					echo '<td>' . $row . '</td>';
					break;
				} else {
					echo '<td>' . $row[$col] . '</td>';
				}
			}
			echo '</tr>';
		}
		echo '</table>';
	}

	/**
	 * Dump two dimensional array as table and die
	 *
	 * @param array $var
	 */
	function ddtable(array $var)
	{
		dtable($var);
		die();
	}

	/**
	 * Dump pretty XML
	 *
	 * @param mixed $xml
	 */
	function dxml($xml)
	{
		if ($xml instanceof DOMElement) {
			$xml->ownerDocument->formatOutput = true;
			$xml->ownerDocument->preserveWhiteSpace = false;
			$xml = $xml->ownerDocument->saveXML($xml);
		} else if ($xml instanceof DOMDocument) {
			$xml->formatOutput = true;
			$xml->preserveWhiteSpace = false;
			$xml = $xml->saveXML();
		} else if ($xml instanceof SimpleXMLElement) {
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->saveXML());
			$xml = $dom->saveXML();
		} else if (is_string($xml)) {
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml);
			$xml = $dom->saveXML();
		}

		$result = '';
		$trace = EnhancedDump\dtrace(debug_backtrace());
		$result .= EnhancedDump\dumpHeader($trace);
		$result .= EnhancedDump\webOnly(htmlspecialchars($xml)) . EnhancedDump\cliOnly($xml);
		$result .= EnhancedDump\dumpFooter($trace);
		echo $result;
	}
}
