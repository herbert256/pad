<?php


  function pTiming_start ($timing) {
    
     if ( $GLOBALS['pExit'] <> 1 or ! $GLOBALS['pTiming'] )
      return;
   
    global $pTimingsCnt, $pTimings_start;

    if ( isset ( $pTimings_start [$timing] ) )
      pError ('tm-oops-1: ' . $timing);

    $pTimingsCnt [$timing] = 1 + ($pTimingsCnt [$timing]??0);

    $pTimings_start [$timing] = microtime(true);

  }


  function pTiming_end ($timing) {

    if ( $GLOBALS['pExit'] <> 1 or ! $GLOBALS['pTiming'] )
      return;

    global $pTimings, $pTimings_start;

    if ( ! isset ( $GLOBALS['pTimings_start'] [$timing] ) )
      pError ('tm-oops-2: ' . $timing);

    $now = microtime(true) - $pTimings_start[$timing];

    $pTimings [$timing] = ($pTimings[$timing]??0) + $now ;
    
    unset($pTimings_start [$timing]);

    foreach ( $pTimings_start as $key => $val ) 
      $pTimings_start [$key] += $now;
    
  }


  function pTiming_close () {

    if ( ! $GLOBALS['pTiming'] )
      return;

    global $pTimings, $pTimings_boot, $pTimings_start, $pTimingsCnt, $pTrace_dir;

    foreach ( $pTimings_start as $key => $val ) 
      pTiming_end ($key);

    $pTimings ['boot'] = $pTimings_boot - $_SERVER['REQUEST_TIME_FLOAT'];

    foreach ($pTimings as $key => $val)
      $pTimings [$key] = (int) ( $val * 1000000 );

    $pTimings ['total'] = (int) ( (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000000 );    

    pHeader ('X-PAD-Timings: ' . json_encode ( $pTimings)       );
    pHeader ('X-PAD-Counts: '  . json_encode ( $pTimingsCnt) );

    if ( $GLOBALS['pTrace_timings'] ) {
      pFile_put_contents ( $pTrace_dir . "/timings.json", $pTimings       );
      pFile_put_contents ( $pTrace_dir . "/counts.json",  $pTimingsCnt );
    }

  }
  
?>