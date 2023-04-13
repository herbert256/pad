<?php


  function padTimingStart ($timing) {
    
     if ( $GLOBALS ['padExit'] <> 1 or ! $GLOBALS ['padTiming'] )
      return;
   
    global $padTimingsCnt, $padTimingsStart;

    $padTimingsCnt [$timing] = 1 + ($padTimingsCnt [$timing]??0);

    $padTimingsStart [$timing] = hrtime(true);

  }


  function padTimingEnd ($timing) {

    if ( $GLOBALS ['padExit'] <> 1 or ! $GLOBALS ['padTiming'] )
      return;

    global $padTimings, $padTimingsStart;

    if ( ! isset ( $GLOBALS ['padTimingsStart'] [$timing] ) )
      return;

    $now = hrtime(true) - $padTimingsStart[$timing];

    $padTimings [$timing] = ($padTimings[$timing]??0) + $now ;
    
    unset($padTimingsStart [$timing]);

    foreach ( $padTimingsStart as $key => $val ) 
      $padTimingsStart [$key] += $now;
    
  }


  function padTimingClose () {

    if ( ! $GLOBALS ['padTiming'] )
      return;

    global $padTimings, $padTimingsBootTime, $padTimingsBootHR, $padTimingsStart, $padTimingsCnt, $padTraceDir;

    foreach ( $padTimingsStart as $key => $val ) 
      padTimingEnd ($key);

    $boot = $padTimingsBootTime - $_SERVER ['REQUEST_TIME_FLOAT'];

    $padTimings ['boot'] = (int) ($boot * 1000000000);
    $padTimings ['pad']  = hrtime(true) - $padTimingsBootHR;    

    foreach ($padTimings as $key => $val)
      $padTimings [$key] = (int) ( $val / 1000 );

    padHeader ('X-PAD-Timings: ' . json_encode ( $padTimings)    );
    padHeader ('X-PAD-Counts: '  . json_encode ( $padTimingsCnt) );

    if ( $GLOBALS ['padTrace'] ) {
      padFilePutContents ( $padTraceDir . "/timings.json", $padTimings    );
      padFilePutContents ( $padTraceDir . "/counts.json",  $padTimingsCnt );
    }

  }
  

?>