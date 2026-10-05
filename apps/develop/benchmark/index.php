<?php

  // Times every page of every application outside the regression family (benchUrls): each
  // page is fetched bare with &padStats, which turns the stats info mode on for that one
  // request when it comes from this machine (inits/info.php), and its PAD-Stats header says
  // how long the request took. The fetches run a few at a time (window=, 4), so the times
  // are under that load and comparable with each other and with earlier runs, not absolute.
  //
  // Every run is kept with its commit (padBenchSave, pad/lib/bench.php) and held against the
  // runs before it: the pages more than 25% slower than their usual time are listed first.
  // ?benchmark/history charts the kept runs.
  //
  // It read the retired DATA/regression/ store, piped through a bm function that went with
  // apps/check, and keyed its rows by page name, so every application's index overwrote
  // the last - it could never show a row.

  set_time_limit ( 300 );

  $title = 'Benchmark';

  $benchUrls    = benchUrls ();
  $benchHistory = padBenchHistory ();
  $benchTimes   = padBenchTimes ( $benchUrls, benchWindow () );

  list ( $benchTimes, $benchSlower ) = padBenchConfirm ( $benchUrls, $benchTimes, $benchHistory );

  $benchRun     = padBenchSave ( $benchTimes );

  $slower = [];

  foreach ( $benchSlower ['pages'] as $benchPage => $benchOne )
    $slower [] = [ 'page' => $benchPage, 'ms' => $benchOne [0], 'usual' => $benchOne [1], 'pct' => $benchOne [2] ];

  $list = [];

  foreach ( $benchTimes as $benchPage => $benchMs )
    $list [$benchPage] = [ 'page' => $benchPage, 'ms' => number_format ( $benchMs, 1 ) ];

  ksort ( $list );

  $benchCount  = count ( $benchUrls );
  $benchShown  = count ( $benchTimes );
  $benchTotal  = $benchRun ['total'];
  $benchCommit = $benchRun ['commit'];
  $benchRuns   = count ( $benchHistory );
  $benchBefore = $benchSlower ['total'] ? $benchSlower ['total'] [2] . '% slower than usual' : 'not slower than usual';

?>
