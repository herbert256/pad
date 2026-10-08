<?php

  // {sudoku '53..7....6..195....98....6.8...6...34..8.3..17...2...6.6....28....419..5....8.79'} -
  // a sudoku as a table, lib/sudoku.php. The first parameter is the puzzle, row by row: a
  // digit for a given, . or 0 for an empty cell; whitespace is passed over. solve fills
  // the empty cells with the solution, set apart in colour from the givens. title= is a
  // caption. A puzzle with a wrong cell count, two equal givens that see each other or -
  // asked to solve - no solution is reported.

  $padSudokuCells = padSudokuCells ( $padParm );

  if ( is_string ( $padSudokuCells ) ) {
    if ( $padCheckSyntax )
      padError ( "the sudoku '" . padMakeSafe ( $padParm, 90 ) . "' is no puzzle - $padSudokuCells" );
    return '';
  }

  $padSudokuSolved = NULL;

  if ( padTagParm ( 'solve', FALSE ) ) {
    $padSudokuSolved = padSudokuSolve ( $padSudokuCells );
    if ( $padSudokuSolved === NULL and $padCheckSyntax )
      padError ( "the sudoku '" . padMakeSafe ( $padParm, 90 ) . "' has no solution" );
  }

  return padSudoku ( $padSudokuCells, $padSudokuSolved, (string) padTagParm ( 'title' ) );

?>
