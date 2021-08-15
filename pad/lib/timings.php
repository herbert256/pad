<?php


  function pad_timing_start ($timing) {
    
    if ( ! $GLOBALS['pad_timing'] )
      return;
    
    $GLOBALS['pad_timings_start'] [$timing] = microtime(true);

  }


  function pad_timing_end ($timing) {
    
    if ( ! $GLOBALS['pad_timing'] )
      return;

    global $pad_timings, $pad_timings_start;

    if ($timing == 'sql' and isset($pad_timings_start ['cache']) )
      return;

    $pad_timings [$timing] = ($pad_timings[$timing]??0) + (microtime(true) - $pad_timings_start[$timing]) ;
    
    unset($pad_timings_start [$timing]);
    
  }

  
  function pad_timing_close () {

    if ( ! $GLOBALS['pad_timing'] )
      return;

    global $pad_timings;
    
    $pad_timings ['all'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

    foreach ($pad_timings as $key => $val)
      $pad_timings [$key] = (int) ( $val * 1000000 );

    pad_header ('X-PAD-Timings: ' . json_encode($pad_timings) );

  }
  
  
?>