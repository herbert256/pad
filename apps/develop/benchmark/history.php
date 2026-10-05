<?php

  // The kept benchmark runs, oldest first: their totals as a line, and a row per run with
  // its commit and how it compared with the runs before it. Shows; never runs one.

  $title = 'Benchmark history';

  $benchHistory = padBenchHistory ();
  $benchChart   = padBenchChart ( $benchHistory );

  $runs = [];

  foreach ( $benchHistory as $benchI => $benchRun ) {

    $benchSlower = padBenchSlower ( $benchRun ['times'], array_slice ( $benchHistory, 0, $benchI ) );

    $runs [] = [
      'when'   => date ( 'Y-m-d H:i:s', $benchRun ['when'] ),
      'commit' => $benchRun ['commit'],
      'pages'  => $benchRun ['pages'],
      'total'  => $benchRun ['total'],
      'slower' => count ( $benchSlower ['pages'] ),
      'trend'  => $benchSlower ['total'] ? '+' . $benchSlower ['total'] [2] . '%' : ''
    ];

  }

  $runs = array_reverse ( $runs );

?>
