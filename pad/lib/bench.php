<?php

  // Benchmark history: every page timed, each run kept with the commit it ran on, and a run
  // held against the runs before it - so a slowdown is caught the way the suites catch an
  // output change, where the develop app's Benchmark showed one run and forgot it.
  //
  //   padBenchTimes   times pages through their PAD-Stats header: [ page => ms ]
  //   padBenchSave    keeps a run as DATA/benchmark/<when>-<commit>.json
  //   padBenchHistory the kept runs, oldest first
  //   padBenchSlower  the pages of a run slower than their usual time by more than a
  //                   threshold, and the run's total likewise
  //   padBenchConfirm the same, after timing each page it named again, one at a time
  //   padBenchChart   the totals of the kept runs as an inline SVG line
  //
  // The develop app's ?benchmark runs, keeps and compares; ?benchmark/history charts the
  // runs; ?benchmark/check is what ./ci.sh asks when CI_BENCH names a threshold.
  //
  // A page's usual time is the median of its times in the last few runs, which one noisy
  // run cannot move much. A page counts as slower only when it lost a few milliseconds as
  // well as the percentage - a floor: a page of one millisecond taking two is the
  // scheduler's jitter, one of one taking twenty is not. And
  // a page found slower is timed again, alone, a few times, before it counts: out of some
  // six hundred pages fetched together a handful came out 50 to 80% slower on every run,
  // a different handful each time.

  function padBenchTimes ( $urls, $window = 12 ) {

    global $padCurlStats;

    $padCurlStats = TRUE;
    $times        = [];

    foreach ( $urls as $name => $url )
      $urls [$name] = $url . ( str_contains ( $url, '?' ) ? '&' : '?' ) . 'padStats';

    foreach ( padCurlMulti ( $urls, $window ) as $name => $curl )
      if ( str_starts_with ( (string) $curl ['result'], '2' ) and isset ( $curl ['stats'] ['total'] ) )
        $times [$name] = round ( $curl ['stats'] ['total'] / 1e6, 2 );

    return $times;

  }

  function padBenchSave ( $times, $dir = 'benchmark' ) {

    $commit = trim ( (string) @shell_exec (
      'git -C ' . escapeshellarg ( dirname ( APPS ) ) . ' rev-parse --short HEAD 2>/dev/null' ) );

    $run = [
      'when'   => time (),
      'commit' => $commit,
      'pages'  => count ( $times ),
      'total'  => round ( array_sum ( $times ), 1 ),
      'times'  => $times
    ];

    $file = "$dir/" . date ( 'Ymd-His', $run ['when'] ) . '-' . ( $commit ?: 'nocommit' ) . '.json';

    padFilePut ( $file, json_encode ( $run ) );

    return $run;

  }

  function padBenchHistory ( $dir = 'benchmark', $max = 100 ) {

    $runs = [];

    foreach ( array_slice ( glob ( DATA . "$dir/*.json" ) ?: [], - $max ) as $file ) {
      $run = json_decode ( (string) file_get_contents ( $file ), TRUE );
      if ( is_array ( $run ) and isset ( $run ['times'] ) )
        $runs [] = $run + [ 'file' => basename ( $file, '.json' ) ];
    }

    usort ( $runs, fn ( $a, $b ) => $a ['when'] <=> $b ['when'] );

    return $runs;

  }

  // What a run did against the $span runs before it: [ 'total' => [ ms, usual, pct ] or
  // NULL, 'pages' => [ page => [ ms, usual, pct ] ] ] for whatever came out more than
  // $threshold percent slower, and at least $floor ms, the slowest first. Only pages the
  // earlier runs timed too are judged; the total is judged over the pages both sides have,
  // so a page added or gone does not move it.

  function padBenchSlower ( $times, $history, $threshold = 25, $floor = 5, $span = 5 ) {

    $history = array_slice ( $history, - max ( 1, (int) $span ) );
    $factor  = 1 + $threshold / 100;
    $pages   = [];
    $now     = 0;
    $usual   = 0;

    foreach ( $times as $page => $ms ) {

      $before = [];

      foreach ( $history as $run )
        if ( isset ( $run ['times'] [$page] ) )
          $before [] = $run ['times'] [$page];

      if ( ! $before )
        continue;

      $median = padBenchMedian ( $before );

      $now   += $ms;
      $usual += $median;

      // A median of 0 ms - a page faster than the clock's 0.1 ms - is counted as 0.1 ms for
      // the percentage: divided by itself it ended the check on a DivisionByZeroError.

      if ( $ms - $median >= $floor and $ms > $median * $factor )
        $pages [$page] = [ $ms, $median, round ( ( $ms - $median ) * 100 / max ( $median, 0.1 ) ) ];

    }

    uasort ( $pages, fn ( $a, $b ) => $b [2] <=> $a [2] );

    $total = ( $usual > 0 and $now > $usual * $factor )
           ? [ round ( $now, 1 ), round ( $usual, 1 ), round ( ( $now - $usual ) * 100 / $usual ) ]
           : NULL;

    return [ 'total' => $total, 'pages' => $pages ];

  }

  // padBenchSlower with a second look: each page it named is fetched again, one at a time,
  // $rounds times, and its median then stands for its time - in the verdict and in the
  // times returned, which are what padBenchSave should keep.

  function padBenchConfirm ( $urls, $times, $history, $threshold = 25, $floor = 5, $rounds = 5 ) {

    $slower = padBenchSlower ( $times, $history, $threshold, $floor );

    if ( ! $slower ['pages'] )
      return [ $times, $slower ];

    $suspects = array_intersect_key ( $urls, $slower ['pages'] );
    $again    = [];

    for ( $i = 0; $i < $rounds; $i++ )
      foreach ( padBenchTimes ( $suspects, 1 ) as $page => $ms )
        $again [$page] [] = $ms;

    foreach ( $again as $page => $list )
      $times [$page] = round ( padBenchMedian ( $list ), 2 );

    return [ $times, padBenchSlower ( $times, $history, $threshold, $floor ) ];

  }

  function padBenchMedian ( $values ) {

    sort ( $values );

    $count = count ( $values );
    $mid   = intdiv ( $count, 2 );

    return ( $count % 2 ) ? $values [$mid] : ( $values [$mid - 1] + $values [$mid] ) / 2;

  }

  // The totals as a line over the runs, scaled to the largest, with a dot per run that says
  // its commit and total when pointed at. Plain SVG, so it needs no script and no library.

  function padBenchChart ( $history, $width = 640, $height = 160 ) {

    if ( count ( $history ) < 1 )
      return '';

    $max   = max ( array_column ( $history, 'total' ) ) ?: 1;
    $count = count ( $history );
    $step  = ( $count > 1 ) ? ( $width - 20 ) / ( $count - 1 ) : 0;
    $line  = [];
    $dots  = '';

    foreach ( array_values ( $history ) as $i => $run ) {
      $x       = round ( 10 + $i * $step, 1 );
      $y       = round ( $height - 10 - ( $run ['total'] / $max ) * ( $height - 20 ), 1 );
      $line [] = "$x,$y";
      $title   = htmlspecialchars ( date ( 'Y-m-d H:i', $run ['when'] ) . ' ' . $run ['commit']
                                  . ' - ' . $run ['total'] . ' ms', ENT_QUOTES );
      $dots   .= "<circle cx=\"$x\" cy=\"$y\" r=\"3\"><title>$title</title></circle>";
    }

    return "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"$width\" height=\"$height\""
         . " viewBox=\"0 0 $width $height\" role=\"img\" aria-label=\"benchmark totals\">"
         . "<polyline fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" points=\""
         . implode ( ' ', $line ) . "\"/>$dots</svg>";

  }

?>
