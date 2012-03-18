<?php
/**
 * Simple variable dump.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function d($var, $label = null)
{
    echo '<div style="background:#f8f8f8;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
    if ($label) {
        echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
    }
    echo dtrace();
    echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
    var_dump($var);
    echo '</pre>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}

/**
 * Dump variable and die.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function dd($var, $label = null)
{
    echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
    if ($label) {
        echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
    }
    echo dtrace();
    echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
    var_dump($var);
    echo '</pre>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
    die();
}

/**
 * Dump variable as string.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function ds($var, $label = null)
{
    echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
    if ($label) {
        echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
    }
    echo dtrace();
    echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
    var_dump((string) $var);
    echo '</pre>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}

/**
 * Dump variable as string and die.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function dsd($x, $label = null)
{
    echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
    if ($label) {
        echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
    }
    echo dtrace();
    echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
    var_dump((string) $var);
    echo '</pre>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
    die();
}

/**
 * Print peak memory usage.
 *
 */
function dmem()
{
    echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
    echo dtrace();
    echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
	echo sprintf('%sK of %s', round(memory_get_peak_usage()/1024), ini_get('memory_limit'));
    echo '</pre>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}

/**
 * Measure execution time.
 *
 * @param array $timers
 * @param type $status
 * @param type $label
 */
function dtimer(&$timers, $status = 0, $label = null)
{
    if (!is_array($timers) || $status === -1) {
        $timers = array();
    }
    $where = dtrace();
    if (null !== $label){
        $where = $label . ' - ' . $where;
    }


    $timers[] = array('where' => $where, 'time' => microtime(true));
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
 * Backtrace.
 *
 * @return string backtrace
 */
function dtrace()
{
    $bt = debug_backtrace();
    $trace = $bt[1];
    $line = $trace['line'];
    $file = basename($trace['file']);
    $function = $trace['function'];
    $class = (isset($bt[2]['class'])?$bt[2]['class']:basename($trace['file']));
    if (isset($bt[2]['class'])) {
        $type = $bt[2]['type'];
    } else {
        $type = ' ';
    }
    $function = isset($bt[2]['function']) ? $bt[2]['function'] : '';
    return sprintf('%s%s%s() line %s <small>(in %s)</small>',$class, $type, $function, $line, $file);
}
