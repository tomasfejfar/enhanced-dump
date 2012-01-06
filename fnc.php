<?php
function decorate($text)
{
    if (php_sapi_name() == 'cli') {
        return;
    }
    echo $text;
}

function cli_decorate($text)
{
    if (php_sapi_name() == 'cli') {
        echo $text;
    }
}

/**
 * Dump variable
 *
 * @param mixed $var The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function d($var, $label = null)
{
    decorate('<div style="background:#f8f8f8;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
    if ($label) {
        echo "<strong>" . $label . "</strong><br />" . PHP_EOL;
    }
    $trace = dtrace();
    echo PHP_EOL . PHP_EOL . 'o----' . $trace . '----o' . PHP_EOL;
    decorate('<pre style="margin:0px;padding:0px;">' . PHP_EOL);
    var_dump($var) . PHP_EOL;
    decorate('</pre>' . PHP_EOL);
    decorate('</div>' . PHP_EOL);
    cli_decorate('o----' . str_repeat('-', strlen($trace)) . '----o' . PHP_EOL . PHP_EOL);
}

/**
 * Dump variable and die.
 *
 * @param mixed $var The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function dd($var, $label = null)
{
    decorate('<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
    if ($label) {
        echo "<strong>" . $label . "</strong><br />" . PHP_EOL;
    }
    $trace = dtrace();
    echo PHP_EOL . PHP_EOL . 'o----' . $trace . '----o' . PHP_EOL;
    decorate('<pre style="margin:0px;padding:0px;">' . PHP_EOL);
    var_dump($var) . PHP_EOL;

    decorate('</pre>' . PHP_EOL);
    decorate('</div>' . PHP_EOL);
    cli_decorate('o----' . str_repeat('-', strlen($trace)) . '----o' . PHP_EOL . PHP_EOL);
    die();
}

/**
 * Dump variable as string
 *
 * @param mixed $var The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function ds($var, $label = null)
{
    decorate('<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
    if ($label) {
        echo "<strong>" . $label . "</strong><br />" . PHP_EOL;
    }
	$trace = dtrace();
    echo PHP_EOL . PHP_EOL . 'o----' . $trace . '----o' . PHP_EOL;
    decorate('<pre style="margin:0px;padding:0px;">' . PHP_EOL);
    var_dump((string) $var) . PHP_EOL;
    decorate('</pre>' . PHP_EOL);
    decorate('</div>' . PHP_EOL);
    cli_decorate('o----' . str_repeat('-', strlen($trace)) . '----o' . PHP_EOL . PHP_EOL);
}

/**
 * Dump variable as string and die.
 *
 * @param mixed $var The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function dsd($var, $label = null)
{
    decorate('<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
    if ($label) {
        echo "<strong>" . $label . "</strong><br />" . PHP_EOL;
    }
    $trace = dtrace();
    echo PHP_EOL . PHP_EOL . 'o----' . $trace . '----o' . PHP_EOL;
    decorate('<pre style="margin:0px;padding:0px;">' . PHP_EOL);
    var_dump((string) $var) . PHP_EOL;
    decorate('</pre>' . PHP_EOL);
    decorate('</div>' . PHP_EOL);
    cli_decorate('o----' . str_repeat('-', strlen($trace)) . '----o' . PHP_EOL . PHP_EOL);
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
    decorate('<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
    $trace = dtrace();
    echo PHP_EOL . PHP_EOL . 'o----' . $trace . '----o' . PHP_EOL;
    decorate('<pre style="margin:0px;padding:0px;">' . PHP_EOL);
    decorate('<textarea style="width:100%;height:100%" onclick="this.select()">' . PHP_EOL);
    echo (string) $var . PHP_EOL;
    decorate('</textarea>');
    decorate('</pre>' . PHP_EOL);
    decorate('</div>' . PHP_EOL);
    cli_decorate('o----' . str_repeat('-', strlen($trace)) . '----o' . PHP_EOL . PHP_EOL);
    die();
}

/**
 * Dump memory usage
 */
function dmem()
{
    decorate('<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
    $trace = dtrace();
    echo PHP_EOL . PHP_EOL . 'o----' . $trace . '----o' . PHP_EOL;
    decorate('<pre style="margin:0px;padding:0px;">' . PHP_EOL);
    cli_decorate('| ');
    echo round(memory_get_peak_usage() / 1024) . 'K of ' . ini_get("memory_limit") . PHP_EOL;
    decorate('</pre>' . PHP_EOL);
    decorate('</div>' . PHP_EOL);
    cli_decorate('o----' . str_repeat('-', strlen($trace)) . '----o' . PHP_EOL . PHP_EOL);
}

/**
 * Timer
 *
 * @param array $timers
 * @param int $status
 * @param null $label
 */
function dtimer(&$timers, $status = 0, $label = null)
{
    if (!is_array($timers) || $status === -1) {
        $timers = [];
    }
    $where = dtrace();
    if (null !== $label) {
        $where = $label . ' - ' . $where;
    }

    $timers[] = [
        'where' => $where,
        'time' => microtime(true),
    ];
    if ($status === 1) {
        echo '<table style="border-color: black;" border="1" cellpadding="3" cellspacing="0">';
        echo '<tr style="background-color:black;color:white;"><th>Trace</th><th>dT [ms]</th><th>dT(cumm) [ms]</th></tr>';
        $lastTime = $timers[0]['time'];
        $firstTime = $timers[0]['time'];
        foreach ($timers as $timer) {
            echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
                $timer['where'],
                sprintf('%01.6f',round(($timer['time'] - $lastTime)*1000,6)),
                sprintf('%01.6f',round(($timer['time'] - $firstTime)*1000,6))
            );
            $lastTime = $timer['time'];
        }
        echo '</table>';
    }
}

/**
 * Dumps stack trace
 *
 * @return string
 */
function dtrace()
{
    $bt = debug_backtrace();
    if (isset($bt[2])) {
        $index = 2;
        $trace = $bt[2];
    } else {
        $index = 1;
        $trace = $bt[1];
        $line = $trace['line'];
        $file = basename($trace['file']);
        $function = $trace['function'];
        return sprintf("php in %s line %s", $file, $line);
    }
    $line = $bt[$index - 1]['line'];
    $class = (isset($trace['class']) ? $trace['class'] : basename($trace['file']));
    if (isset($trace['class'])) {
        $type = $trace['type'];
    } else {
        $type = ' ';
    }
    $function = isset($trace['function']) ? $trace['function'] : '';
    return sprintf("%s%s%s() line %s <small>(in %s)</small>", $class, $type, $function, $line, $file);
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
    $xml = null;
    if ($xml instanceof DOMElement) {
        $xml = $xml->ownerDocument->saveXML($xml);
    } else if ($xml instanceof DOMDocument) {
        $xml = $xml->saveXML();
    } else if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->saveXML();
    } else if (is_string($xml)) {
        $xml = $xml;
    }

    // make the XML readable by human eye!
    if (!is_null($xml)) {
        $er = error_reporting();
        error_reporting(0); // PEAR has lots of strict errors
        if (class_exists('XML_Beautifier')) {
            $fmt = new XML_Beautifier();
            $xml = PHP_EOL . $fmt->formatString($xml, "Plain");
        }
        error_reporting($er);
    }

    decorate('<div style="background:#f8f8f8;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL);
    $trace = dtrace();
    echo PHP_EOL . PHP_EOL . 'o----' . $trace . '----o' . PHP_EOL;
    decorate('<pre style="margin:0px;padding:0px;">' . PHP_EOL);
    var_dump($xml) . PHP_EOL;
    if (!is_null($xml)) {
        if (class_exists('Zend_Debug')) { // replaces < > with &lt; &gt;
            Zend_Debug::dump($xml) . PHP_EOL;
        } else {
            var_dump(htmlspecialchars($xml)) . PHP_EOL;
        }
    }
    decorate('</pre>' . PHP_EOL);
    decorate('</div>' . PHP_EOL);
    cli_decorate('o----' . str_repeat('-', strlen($trace)) . '----o' . PHP_EOL . PHP_EOL);
}
