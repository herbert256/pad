<?php

  if ( isset($_REQUEST['padLog']) )
    $padLog = TRUE;

  if ( ! $padLog )
    return;

  $now   = microtime(true);
  $sec   = floor($now);
  $micro = (int) (($now - $sec) * 1000000);
  $micro = str_pad($micro, 6, '0', STR_PAD_LEFT);

  $GLOBALS ['padLogFile'] = "log/$app/$page/" . date('ymd-His-') . $micro . ".txt";

?>