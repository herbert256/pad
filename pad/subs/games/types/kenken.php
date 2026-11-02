<?php

  // kenken - Authored arithmetic cages on size 3..6. Default 4x4 puzzle is uniquely solvable. `puzzle=` accepts size plus cages with zero-based cell indices, op (+,*,-,/,=), integer target. Cages must partition all cells and be edge connected, maximum six cells each; subtraction/division have two, equality one. A bounded Latin-square solver verifies uniqueness before display; statuses distinguish invalid definitions, unsolved, multiple solutions and exhausted 100000-node budget. No generated puzzle is presented unchecked. Browser checks row/column uniqueness and all operations; accessible labelled numeric controls, Check and Reset.
  //
  // ## Example
  //
  // ```pad
  // {game:kenken/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/game-kenken.php`.

$puzzle=padTagParm('puzzle',padKenkenDefault());
try{$answer=padKenkenSolve($puzzle);if($answer['status']!=='unique')throw new InvalidArgumentException('KenKen puzzle status: '.$answer['status']);return padGame('kenken','Arithmetic cages',$puzzle,'<p>Fill each row and column with 1 to the board size, once each. Every labelled cage must meet its target and operation.</p>');}catch(InvalidArgumentException $e){padError($e->getMessage());return '';}
