<?php


  function padTimingStart ($timing) {
    
     if ( ! $GLOBALS ['padTiming'] or $GLOBALS ['padExit'] <> 1 )
      return;

    global $padTimingsCnt, $padTimingsStart;

    $padTimingsCnt [$timing.'C'] = 1 + ($padTimingsCnt [$timing.'C']??0);

    $padTimingsStart [$timing] = hrtime(true);

  }


  function padTimingEnd ($timing) {

    if ( ! $GLOBALS ['padTiming'] or $GLOBALS ['padExit'] <> 1 or ! isset ( $GLOBALS ['padTimingsStart'] [$timing] ) )
      return;

    global $padTimings, $padTimingsStart;

    $now = hrtime(true) - $padTimingsStart[$timing];

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

    $padTimings ['pad']  = hrtime(true) - $padTimingsBoot;    

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