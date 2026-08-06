<?php

  // Range setup for strong, run before generation: the sequence is positive, so a run
  // told to start lower - {strong from=0}, or a negative from - begins at the first
  // candidate that could belong to it rather than spending the try limit outside it.
  //
  // from is the candidate itself here, this being a bool type, so the floor is the lowest
  // value the predicate can accept rather than a position in a list.

  if ( $pqFrom < 1 )
    $pqFrom = 1;

?>