<?php
  $allocationProblem = [ 'rows' => [ 'Ada', 'Bo', 'Cy' ], 'columns' => [ 'Design', 'Build', 'Test' ], 'costs' => [ [ 2, 8, NULL ], [ 6, 3, 7 ], [ 5, NULL, 2 ] ] ];
  $allocation = padSolve ( 'assignment', $allocationProblem );
  $allocationRows = $allocation ['solution'] ?? [];
  $capabilities = [];
  foreach ( $allocationProblem ['costs'] as $i => $costs ) foreach ( $costs as $j => $cost ) if ( $cost !== NULL ) $capabilities [] = [ $allocationProblem ['rows'] [$i], $allocationProblem ['columns'] [$j] ];
  $matching = padSolve ( 'matching', [ 'left' => $allocationProblem ['rows'], 'right' => $allocationProblem ['columns'], 'edges' => $capabilities ], [ 'requirePerfect' => TRUE ] );
?>
