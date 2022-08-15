<?php


  function padTimingStart ($timing) {
    
     if ( $GLOBALS ['padExit'] <> 1 or ! $GLOBALS ['padTiming'] )
      return;
   
    global $padTimingsCnt, $padTimings_start;

#    if ( isset ( $padTimings_start [$timing] ) )
#      padError ('tm-oops-1: ' . $timing);

    $padTimingsCnt [$timing] = 1 + ($padTimingsCnt [$timing]??0);

    $padTimings_start [$timing] = microtime(true);

  }


  function padTimingEnd ($timing) {

    if ( $GLOBALS ['padExit'] <> 1 or ! $GLOBALS ['padTiming'] )
      return;

    global $padTimings, $padTimings_start;

    if ( ! isset ( $GLOBALS ['padTimings_start'] [$timing] ) )
      return;
#      padError ('tm-oops-2: ' . $timing);

    $now = microtime(true) - $padTimings_start[$timing];

    $padTimings [$timing] = ($padTimings[$timing]??0) + $now ;
    
    unset($padTimings_start [$timing]);

    foreach ( $padTimings_start as $key => $val ) 
      $padTimings_start [$key] += $now;
    
  }


  function padTimingClose () {

    if ( ! $GLOBALS ['padTiming'] )
      return;

    global $padTimings, $padTimings_boot, $padTimings_start, $padTimingsCnt, $padTraceDir;

    foreach ( $padTimings_start as $key => $val ) 
      padTimingEnd ($key);

    $padTimings ['boot'] = $padTimings_boot - $_SERVER['REQUEST_TIME_FLOAT'];

    foreach ($padTimings as $key => $val)
      $padTimings [$key] = (int) ( $val * 1000000 );

    $padTimings ['total'] = (int) ( (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000000 );    

    padHeader ('X-PAD-Timings: ' . json_encode ( $padTimings)       );
    padHeader ('X-PAD-Counts: '  . json_encode ( $padTimingsCnt) );

    if ( $GLOBALS ['padTrace'] ) {
      padFilePutContents ( $padTraceDir . "/timings.json", $padTimings       );
      padFilePutContents ( $padTraceDir . "/counts.json",  $padTimingsCnt );
    }

  }
  
?>