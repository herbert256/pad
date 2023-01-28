<?php

  ini_set('max_execution_time', '5');

  $title = "Benchmark";

  $type = 'timings';

  $files = $totals = [];

  $bench = ['benchmark', 'overhead', 'get', 'network', 'total', 'boot', 'init','tag', 'var', 'eval', 'opt', 'app', 'read', 'write', 'curl', 'sql', 'cache', 'exit', 'other'];

  $not = ['benchmark', 'overhead', 'get', 'network', 'total', 'boot', 'init', 'exit', 'other'];

  if ( $type == 'count' )
    foreach ( $bench as $key => $val )
      if ( in_array($val, $not))
        unset($bench[$key]);

  foreach ($bench as $key => $val)
    $totals [$key] ['total'] = 0;

  $ref = get_reference_files ();

  foreach ($ref as $item) {

    $curl = padComplete ('reference', $item);

    $files [$item] ['item'] = $item;

    foreach ($bench as $wrk)
      $files [$item] [$wrk] = 0;

    $files [$item] ['not'] = 0;

    for ($i = 0; $i < 1; $i++) {

      $start = microtime(true);
      $curl  = padComplete ('reference', $item);
      $end   = microtime(true);

      if ( $curl ['result'] <> 200 ) {
        unset ( $files [$item] );
        continue 2;
      }

      if ( $type == 'timings' ) {

        $files [$item] ['benchmark'] += (int) ( ($end - $start) * 1000000 );
        $files [$item] ['get']       += (int) (  $curl ['info'] ['total_time'] * 1000000 );

        $timings = isset ($curl ['headers'] ['X-PAD-Timings']) ? json_decode ($curl ['headers'] ['X-PAD-Timings'], TRUE) : [];  

        foreach ( $timings as $key => $val )
          $files [$item] [$key] += $val;

        foreach ( $timings as $key => $val )
          if ( $key <> 'total' )
            $files [$item] ['not'] += $val;

      }

      if ( $type == 'count' ) {

        $timings = isset ($curl ['headers'] ['X-PAD-Counts']) ? json_decode ($curl ['headers'] ['X-PAD-Counts'], TRUE) : [];  

        foreach ( $timings as $key => $val )
          if ( in_array($key, $bench) )
            $files [$item] [$key] += $val;

      }

    }
   
  }

  if ( $type == 'timings' )
    foreach ($files as $item => $record ) {
      $files [$item] ['overhead'] = $record ['benchmark'] - $record ['get'];
      $files [$item] ['network']  = $record ['get'] - $record ['total'];
      $files [$item] ['other']    = $record ['total'];
    }

  if ( $type == 'timings' )
    foreach ($files as $item => $record )
      $files [$item] ['other'] -= $record ['not'];

  foreach ($files as $item => $record ) 
    foreach ( $bench as $key => $val ) 
      $totals [$key] ['total'] += $record [$val];

  $now = [];

  foreach ( $bench as $key => $val ) 
    $now [''] [$val] = $totals [$key]['total'];

  foreach ( $files as $item => $record )
    foreach ( $bench as $val ) 
      $now [$item] [$val] = $record [$val];

?>