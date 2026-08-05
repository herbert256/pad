<?php

  // Range setup for random, run before generation: fixes the window the draws come from.
  //
  // $pqRandomStart and $pqRandomEnd are minimal= and maximal=, with 1 standing in when no
  // minimal= was given (its default is PHP_INT_MIN). An increment other than 1 turns the
  // window into $pqRandomSteps discrete steps, which loop.php uses to snap each draw onto
  // the grid.

  $pqRandomStart = ( $pqMin == PHP_INT_MIN ) ? 1 : $pqMin;
  $pqRandomEnd   = $pqMax;

  if ( $pqInc != 1 )
    $pqRandomSteps = intval ( ( $pqRandomEnd - $pqRandomStart ) / $pqInc );

?>