<?php

  // sudoku - fill the grid so that every row, column and box of three by three holds the
  // digits 1 to 9 once. The puzzle is the first parameter, row by row - a digit for a given,
  // . or 0 for an empty square, whitespace and the | and + of a drawn grid passed over - or
  // without one, a new puzzle with one solution each game, the same for the same seed=,
  // givens= how many squares it shows (24 to 45, 30 unless given). Select a square and type
  // a digit or click one; notes writes small candidates, hint fills a square, check marks
  // the wrong ones. paper draws the puzzle as a table to print instead, solve with its
  // solution filled in; title= names it.
  //
  //   {game:sudoku '53..7....6..195....98....6.8...6...34..8.3..17...2...6.6....28....419..5....8..79'/}
  //   {game:sudoku givens=26, seed=7/}
  //   {game:sudoku $puzzle, solve/}

  $padSudokuGiven = padGameValue ();

  if ( $padSudokuGiven === '' ) {

    if ( padGameFlag ( 'solve' ) and $GLOBALS ['padCheckSyntax'] )
      padError ( "the sudoku to solve has no puzzle - {game:sudoku '53..7....', solve/}" );

    return padGame ( 'sudoku', 'Sudoku', [ 'givens' => padGameInt ( 'givens', 30, 24, 45 ) ] );

  }

  $padSudokuCells = padSudokuCells ( $padSudokuGiven );

  if ( is_string ( $padSudokuCells ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the sudoku '" . padMakeSafe ( $padSudokuGiven, 90 ) . "' is no puzzle - $padSudokuCells" );
    return '';
  }

  if ( padGameFlag ( 'solve' ) ) {
    $padSudokuSolved = padSudokuSolve ( $padSudokuCells, $padSudokuMany );
    if ( $padSudokuMany and $GLOBALS ['padCheckSyntax'] )
      padError ( "the sudoku '" . padMakeSafe ( $padSudokuGiven, 90 ) . "' has more than one solution - a given is missing" );
    elseif ( $padSudokuSolved === NULL and $GLOBALS ['padCheckSyntax'] )
      padError ( "the sudoku '" . padMakeSafe ( $padSudokuGiven, 90 ) . "' has no solution" );
    elseif ( $padSudokuSolved === FALSE and $GLOBALS ['padCheckSyntax'] )
      padError ( "the sudoku '" . padMakeSafe ( $padSudokuGiven, 90 ) . "' is too hard to solve here - it may have no solution, or many" );
    return padSudoku ( $padSudokuCells, $padSudokuSolved ?: NULL, padParmText ( padTagParm ( 'title' ) ) );
  }

  if ( padGameFlag ( 'paper' ) )
    return padSudoku ( $padSudokuCells, NULL, padParmText ( padTagParm ( 'title' ) ) );

  // Played, the puzzle needs a solution: the hint and the check go by it. One with more than
  // one is played as well - a grid full with nothing clashing is solved, whichever solution
  // it is - and the status says so.

  $padSudokuSolved = padSudokuSolve ( $padSudokuCells, $padSudokuMany );

  if ( ! $padSudokuSolved ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the sudoku '" . padMakeSafe ( $padSudokuGiven, 90 ) . "' "
                 . ( $padSudokuSolved === NULL ? 'has no solution' : 'is too hard to solve here - it may have no solution, or many' ) );
    return '';
  }

  return padGame ( 'sudoku', 'Sudoku', [ 'puzzle' => implode ( '', $padSudokuCells ), 'solution' => implode ( '', $padSudokuSolved ),
                                          'many' => (bool) $padSudokuMany ] );

?>
