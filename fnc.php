<?php
/**
 * Simple dump.
 *
 * @param mixed $x input
 */
function d($x)
{
  echo '<div style="background:#f8f8f8;margin:5px;padding:5px;border: solid grey 1px;">'."\r\n";
  echo dtrace();
  echo '<pre style="margin:0px;padding:0px;">'."\r\n";
  var_dump($x);
  echo '</pre>'."\r\n";
  echo '</div>'."\r\n";
}

/**
 * Dump and die.
 *
 * @param mixed $x input
 */
function dd($x)
{
  echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">'."\r\n";
  echo dtrace();
  echo '<pre style="margin:0px;padding:0px;">'."\r\n";
  var_dump($x);
  echo '</pre>'."\r\n";
  echo '</div>'."\r\n";
  die();
}

/**
 * Dump as string.
 *
 * @param mixed $x input
 */
function ds($x)
{
  echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">'."\r\n";
  echo dtrace();
  echo '<pre style="margin:0px;padding:0px;">'."\r\n";
  var_dump((string)$x);
  echo '</pre>'."\r\n";
  echo '</div>'."\r\n";
}

/**
 * Dump as string and die.
 *
 * @param mixed $x input
 */
function dsd($x)
{
  echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">'."\r\n";
  echo dtrace();
  echo '<pre style="margin:0px;padding:0px;">'."\r\n";
  var_dump((string)$x);
  echo '</pre>'."\r\n";
  echo '</div>'."\r\n";
  die();
}

/**
 * Print peak memory usage.
 *
 * @param mixed $x input
 */
function dmem()
{
    echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">'."\r\n";
    echo dtrace();
    echo '<pre style="margin:0px;padding:0px;">'."\r\n";
    echo round(memory_get_peak_usage()/1024) . 'K of '.ini_get("memory_limit");
    echo '</pre>'."\r\n";
    echo '</div>'."\r\n";
}

/**
 * Measure execution time.
 *
 * @param array $timers
 * @param type $status
 * @param type $label
 */
function dtimer(&$timers, $status = 0, $label = null)
{ if (!is_array($timers) || $status === -1) {
    $timers = array();
  }
  $where = dtrace();
  if(null !== $label){
    $where = $label . ' - ' . $where;
  }


  $timers[] = array('where' => $where, 'time' => microtime(true));
  if ($status === 1) {
    echo '<table style="border-color: black;" border="1" cellpadding="3" cellspacing="0">';
    echo '<tr style="background-color:black;color:white;"><th>Trace</th><th>dT</th><th>dT(cumm)</th></tr>';
    $lastTime = $timers[0]['time'];
    $firstTime = $timers[0]['time'];
    foreach ($timers as $timer) {
      echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
        $timer['where'],
        sprintf('%01.6f',round($timer['time'] - $lastTime,6)),
        sprintf('%01.6f',round($timer['time'] - $firstTime,6))
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
  return sprintf("%s%s%s() line %s <small>(in %s)</small>",$class, $type, $function, $line, $file);
}
