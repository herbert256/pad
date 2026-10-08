<?php

  $tagAbout   = 'A sudoku as a table, the givens bold - and with solve, solved on the server.';
  $tagGroup   = 'games';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{sudoku 'puzzle'}
{sudoku 'puzzle', solve, title='Caption'}
PAD;

  $tagParms   = [
    'puzzle' => 'The 81 cells row by row: a digit 1-9 for a given, <code>.</code> or <code>0</code> for an open cell. Whitespace is passed over, so nine lines will do.' ];

  $tagOptions = [
    'solve' => 'Bare option: fill the open cells with the solution, in another colour than the givens.',
    'title' => 'A caption for the table. Without it the table is named by <code>aria-label</code> (<code>Sudoku, 30 givens</code>).' ];

  $tagSee     = [ 'chess', 'crossword' ];

?>
