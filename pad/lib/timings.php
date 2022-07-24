<?php


  function pad_timing_start ($timing) {
    
     if ( $GLOBALS['pad_exit'] <> 1 )
      return;
         
    if ( ! $GLOBALS['pad_timing'] )
      return;
   
    global $pad_timings_count, $pad_timings_start;

    if ( isset ( $pad_timings_start [$timing] ) )
      pad_error ('tm-oops-1: ' . $timing);

    $pad_timings_count [$timing] = 1 + ($pad_timings_count [$timing]??0);

    $pad_timings_start [$timing] = microtime(true);

  }


  function pad_timing_end ($timing) {

    if ( $GLOBALS['pad_exit'] <> 1 )
      return;
    
    if ( ! $GLOBALS['pad_timing'] )
      return;

    global $pad_timings, $pad_timings_start;

    if ( ! isset ( $GLOBALS['pad_timings_start'] [$timing] ) )
      pad_error ('tm-oops-2: ' . $timing);

    $now = microtime(true) - $pad_timings_start[$timing];

    $pad_timings [$timing] = ($pad_timings[$timing]??0) + $now ;
    
    unset($pad_timings_start [$timing]);

    foreach ( $pad_timings_start as $key => $val ) 
      $pad_timings_start [$key] += $now;
    
  }


  function pad_timing_close () {

    if ( ! $GLOBALS['pad_timing'] )
      return;

    global $pad_timings, $pad_timings_boot, $pad_timings_start, $pad_timings_count, $pad_trace_dir_base;

    foreach ( $pad_timings_start as $key => $val ) 
      pad_timing_end ($key);

    $pad_timings ['boot'] = $pad_timings_boot - $_SERVER['REQUEST_TIME_FLOAT'];

    foreach ($pad_timings as $key => $val)
      $pad_timings [$key] = (int) ( $val * 1000000 );

    $pad_timings ['total'] = (int) ( (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000000 );    

    pad_header ('X-PAD-Timings: ' . json_encode ( $pad_timings)       );
    pad_header ('X-PAD-Counts: '  . json_encode ( $pad_timings_count) );

    if ( $GLOBALS['pad_trace_timings'] ) {
      pad_file_put_contents ( $pad_trace_dir_base . "/timings.json", $pad_timings       );
      pad_file_put_contents ( $pad_trace_dir_base . "/counts.json",  $pad_timings_count );
    }

  }
  
?>