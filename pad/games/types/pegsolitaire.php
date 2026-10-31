<?php

  // pegsolitaire - peg solitaire on the English cross of 33 holes, every hole but the middle
  // one filled: a peg jumps over a neighbour into an empty hole two away, across or down,
  // and the peg jumped over is taken off. One peg left wins - in the middle, a perfect game.
  // Click a peg, then the hole it jumps to; undo takes a jump back.

  return padGame ( 'pegsolitaire', 'Peg solitaire' );

?>
