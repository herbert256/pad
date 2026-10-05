<?php

  // The benchmark history's verdict over made-up runs: a page counts as slower past the
  // threshold and the floor both, against its median over the runs before; a page without
  // history is not judged, and the total is held to the same threshold. A run kept in a
  // directory of its own is read back by the history, and the chart draws a dot per run.

  $benchBefore = [
    [ 'when' => 1, 'commit' => 'aaa', 'total' => 111, 'times' => [ 'a' => 10, 'b' => 100, 'c' => 1 ] ],
    [ 'when' => 2, 'commit' => 'bbb', 'total' => 113, 'times' => [ 'a' => 12, 'b' => 100, 'c' => 1 ] ],
    [ 'when' => 3, 'commit' => 'ccc', 'total' => 122, 'times' => [ 'a' => 11, 'b' => 110, 'c' => 1 ] ],
  ];

  $benchSome = padBenchSlower ( [ 'a' => 20, 'b' => 105, 'c' => 3, 'd' => 50 ], $benchBefore, 25, 5 );
  $benchAll  = padBenchSlower ( [ 'a' => 30, 'b' => 150, 'c' => 1 ],            $benchBefore, 25, 5 );

  $slowPages = [];
  foreach ( $benchSome ['pages'] as $benchPage => $benchOne )
    $slowPages [] = "$benchPage " . implode ( '/', $benchOne );

  $slowPages = implode ( ', ', $slowPages );
  $slowTotal = $benchSome ['total'] ? implode ( '/', $benchSome ['total'] ) : 'not slower';
  $allPages  = implode ( ', ', array_keys ( $benchAll ['pages'] ) );
  $allTotal  = implode ( '/', $benchAll ['total'] );

  @array_map ( 'unlink', glob ( DATA . 'zzbench/*' ) ?: [] );

  padBenchSave ( [ 'x' => 1.5, 'y' => 2.5 ], 'zzbench' );
  $benchKept = padBenchHistory ( 'zzbench' );
  $keptRuns  = count ( $benchKept ) . ' run, ' . $benchKept [0] ['pages'] . ' pages, ' . $benchKept [0] ['total'] . ' ms';

  @array_map ( 'unlink', glob ( DATA . 'zzbench/*' ) ?: [] );
  @rmdir ( DATA . 'zzbench' );

  $chartDots = substr_count ( padBenchChart ( $benchBefore ), '<circle' );

?>
