<?php

  $tagAbout   = 'A chess position from FEN as an SVG board, with marked squares and arrows.';
  $tagGroup   = 'games';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{chess 'fen'}
{chess 'fen', flip, highlight='e2, e4', arrow='e2-e4, g8-f6', size=320, title='Name'}
PAD;

  $tagParms   = [
    'fen' => 'The position in FEN. The piece placement alone will do; side to move, castling, en passant and the two counters are checked when written.' ];

  $tagOptions = [
    'flip'      => 'Bare option: turn the board, Black at the bottom.',
    'highlight' => 'Squares to mark, separated by commas: <code>\'e2, e4\'</code>.',
    'arrow'     => 'Arrows from square to square, separated by commas: <code>\'e2-e4, g1-f3\'</code>.',
    'size'      => 'Width and height in pixels, 360 when not given (80 at least); the board shrinks to fit a narrower container.',
    'title'     => 'The accessible name of the board. <code>Chess position, White to move</code> when not given.' ];

  $tagSee     = [ 'sudoku', 'crossword', 'chart' ];

?>
