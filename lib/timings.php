<?php


  function padTimingStart ($timing) {
    
     if ( $GLOBALS ['padExit'] <> 1 or ! $GLOBALS ['padTiming'] )
      return;
   
    global $padTimingsCnt, $padTimingsStart;

    $padTimingsCnt [$timing] = 1 + ($padTimingsCnt [$timing]??0);

    $padTimingsStart [$timing] = microtime(true);

  }


  function padTimingEnd ($timing) {

    if ( $GLOBALS ['padExit'] <> 1 or ! $GLOBALS ['padTiming'] )
      return;

    global $padTimings, $padTimingsStart;

    if ( ! isset ( $GLOBALS ['padTimingsStart'] [$timing] ) )
      return;

    $now = microtime(true) - $padTimingsStart[$timing];

    $padTimings [$timing] = ($padTimings[$timing]??0) + $now ;
    
    unset($padTimingsStart [$timing]);

    foreach ( $padTimingsStart as $key => $val ) 
      $padTimingsStart [$key] += $now;
    
  }


  function padTimingClose () {

    if ( ! $GLOBALS ['padTiming'] )
      return;

    global $padTimings, $padTimingsBoot, $padTimingsStart, $padTimingsCnt, $padTraceDir;

    foreach ( $padTimingsStart as $key => $val ) 
      padTimingEnd ($key);

    $padTimings ['boot']  = $padTimingsBoot - $_SERVER['REQUEST_TIME_FLOAT'];
    $padTimings ['total'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];    

    foreach ($padTimings as $key => $val)
      $padTimings [$key] = (int) ( $val * 1000000 );

    padHeader ('X-PAD-Timings: ' . json_encode ( $padTimings)    );
    padHeader ('X-PAD-Counts: '  . json_encode ( $padTimingsCnt) );

    if ( $GLOBALS ['padTrace'] ) {
      padFilePutContents ( $padTraceDir . "/timings.json", $padTimings    );
      padFilePutContents ( $padTraceDir . "/counts.json",  $padTimingsCnt );
    }

  }
  

?>