<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $path ) { return scandir ( $path ); }
  function padError ( $message ) { throw new InvalidArgumentException ( $message ); }
  require PAD . 'lib/subsystem.php';
  foreach ( [ 'statistics', 'graphs', 'textanalysis' ] as $library ) require PAD . "lib/$library.php";
  $checks = 0;
  function check ( $condition, $message ) { global $checks; $checks++; if ( ! $condition ) throw new RuntimeException ( $message ); }
  function rejects ( $call, $message ) { try { $call (); } catch ( InvalidArgumentException $e ) { check ( str_contains ( $e->getMessage (), 'finite' ), $message ); return; } throw new RuntimeException ( $message ); }

  // Valid, tiny rates must not leak infinity into an otherwise JSON-safe result.
  rejects ( fn () => padTextAnalysis ( 'counts', 'one', [ 'wordsPerMinute' => 1e-320 ] ), 'reading time overflow is explicit' );
  check ( padTextAnalysis ( 'counts', '', [ 'wordsPerMinute' => 1e-320 ] ) ['estimatedReadingMinutes'] === 0.0, 'zero words at a tiny rate' );
  $reading = padTextAnalysis ( 'counts', 'one two', [ 'wordsPerMinute' => 1e-300 ] );
  check ( is_finite ( $reading ['estimatedReadingMinutes'] ) && json_encode ( $reading ) !== FALSE, 'representable reading time remains serializable' );

  // Orthogonal centered series have zero slope even if their scale ratio overflows.
  $regression = padStats ( 'regression', [ [ 'x' => -1e-200, 'y' => 1e200 ], [ 'x' => 0, 'y' => -2e200 ], [ 'x' => 1e-200, 'y' => 1e200 ] ] );
  foreach ( [ 'slope', 'intercept', 'r', 'rSquared' ] as $key ) check ( $regression [$key] === 0.0, 'orthogonal regression ' . $key );
  check ( json_encode ( $regression ) !== FALSE, 'regression remains serializable' );
  // Conversely, multiplying before dividing can itself overflow unnecessarily.
  $pairs = [ [ 'x' => 0, 'y' => 0 ], [ 'x' => -1e308, 'y' => -1e308 ], [ 'x' => 1e308, 'y' => 1e308 ] ];
  for ( $i = 0; $i < 10; $i++ ) { $pairs [] = [ 'x' => -2e307, 'y' => -1e308 ]; $pairs [] = [ 'x' => 2e307, 'y' => 1e308 ]; }
  $wide = padStats ( 'regression', $pairs );
  check ( abs ( $wide ['slope'] - 15 / 7 ) < 1e-12 && $wide ['intercept'] === 0.0, 'representable scale ratio prevents intermediate product overflow' );
  rejects ( fn () => padStats ( 'regression', [ [ 'x' => 0, 'y' => 0 ], [ 'x' => 1e-200, 'y' => 1e200 ] ] ), 'actual slope overflow remains explicit' );

  $cycle = padGraph ( 'shortestpath', [ 'a', 'b' ], [ [ 'a', 'b', 1e308 ], [ 'b', 'a', 1e308 ] ], [ 'source' => 'a', 'target' => 'b' ] );
  check ( $cycle ['path'] === [ 'a', 'b' ] && array_column ( $cycle ['distances'], 'distance' ) === [ 0.0, 1e308 ], 'settled neighbors cannot cause false overflow' );
  $alternate = padGraph ( 'shortestpath', [ 'a', 'b', 'c', 'd' ], [ [ 'a', 'b', 1e308 ], [ 'a', 'c', 1.1e308 ], [ 'b', 'd', 1e308 ], [ 'c', 'd', 1 ] ], [ 'source' => 'a', 'target' => 'd' ] );
  check ( $alternate ['path'] === [ 'a', 'c', 'd' ] && $alternate ['distances'] [3] ['distance'] === 1.1e308, 'later finite alternate route replaces overflow candidate' );
  $known = padGraph ( 'shortestpath', [ 'a', 'b', 'c' ], [ [ 'a', 'b', 1e308 ], [ 'a', 'c', 1.5e308 ], [ 'b', 'c', 1e308 ] ], [ 'source' => 'a' ] );
  check ( $known ['distances'] [2] ['distance'] === 1.5e308, 'known finite unsettled distance survives overflowing detour' );
  rejects ( fn () => padGraph ( 'shortestpath', [ 'a', 'b', 'c' ], [ [ 'a', 'b', 1e308 ], [ 'b', 'c', 1e308 ] ], [ 'source' => 'a' ] ), 'actual shortest distance overflow remains explicit' );
  $disconnected = padGraph ( 'shortestpath', [ 'a', 'b', 'c' ], [ [ 'b', 'c', 1e308 ], [ 'c', 'b', 1e308 ] ], [ 'source' => 'a', 'target' => 'c' ] );
  check ( $disconnected ['status'] === 'unreachable', 'unreachable huge cycle stays unreachable' );
  echo "analysis numeric edges: $checks assertions passed\n";
?>
