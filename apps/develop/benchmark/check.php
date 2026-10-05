<?php

  // The benchmark as a gate, for ./ci.sh: one run, kept, held against the runs before it.
  // Answers plain text - the first line "benchmark ok" or "benchmark slower", then one line
  // per finding - which the gate prints and judges. threshold= is the percentage a page or
  // the total may be slower than its usual time (25), floor= the milliseconds a page must
  // have lost as well before it counts (5), window= how many pages are fetched at once (4).

  set_time_limit ( 300 );

  $benchThreshold = max ( 1, (int)   padRequest ( 'threshold', 25 ) );
  $benchFloor     = max ( 0, (float) padRequest ( 'floor',     5  ) );

  $benchUrls    = benchUrls ();
  $benchHistory = padBenchHistory ();
  $benchTimes   = padBenchTimes ( $benchUrls, benchWindow () );

  list ( $benchTimes, $benchSlower )
    = padBenchConfirm ( $benchUrls, $benchTimes, $benchHistory, $benchThreshold, $benchFloor );

  $benchRun     = padBenchSave ( $benchTimes );

  $benchLines = [];

  if ( ! $benchHistory )
    $benchLines [] = 'the first kept run - nothing to compare with yet';

  if ( $benchSlower ['total'] )
    $benchLines [] = vsprintf ( 'total %s ms, usually %s ms: %s%% slower', $benchSlower ['total'] );

  foreach ( $benchSlower ['pages'] as $benchPage => $benchOne )
    $benchLines [] = "$benchPage " . vsprintf ( '%s ms, usually %s ms: %s%% slower', $benchOne );

  $benchVerdict = ( $benchSlower ['total'] or $benchSlower ['pages'] ) ? 'benchmark slower' : 'benchmark ok';

  $padContentType = 'text/plain';

  echo "$benchVerdict\n";
  echo $benchRun ['pages'] . ' pages, ' . $benchRun ['total'] . " ms, commit " . $benchRun ['commit'] . "\n";

  foreach ( $benchLines as $benchLine )
    echo "$benchLine\n";

?>
