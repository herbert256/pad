<?php
  $coverPuzzle = [ 'columns' => [ 'A1', 'A2', 'B1', 'B2' ], 'rows' => [ 'Top horizontal' => [ 'A1', 'A2' ], 'Bottom horizontal' => [ 'B1', 'B2' ], 'Left vertical' => [ 'A1', 'B1' ], 'Right vertical' => [ 'A2', 'B2' ] ] ];
  $coverResult = padSolve ( 'exactcover', $coverPuzzle );
  $coverRows = [];
  foreach ( $coverResult ['solution'] ?? [] as $piece ) $coverRows [] = [ 'piece' => $piece, 'cells' => implode ( ', ', $coverPuzzle ['rows'] [$piece] ) ];
?>
