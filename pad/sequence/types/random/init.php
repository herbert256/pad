<?php

  // Range setup for random, run before generation: fixes the window the draws come from.
  //
  // $pqRandomStart and $pqRandomEnd are minimal= and maximal=, with 1 standing in when no
  // minimal= was given (its default is PHP_INT_MIN). An increment other than 1 turns the
  // window into $pqRandomSteps discrete steps, which loop.php uses to snap each draw onto
  // the grid.

  // A numeric parameter is the one-in-how-many of {random 4}, which has to be 1 or more for
  // there to be a draw at all; anything else it may hold - a '50%', a store - is loop.php's
  // to read, so only a number is checked here. A parameter of 0 is how loop.php reads "none
  // given", and goes on to draw from the window like a bare {random}.

  if ( $pqParm and is_numeric ( $pqParm ) and $pqParm < 1 )
    return padError ( 'The random sequence needs a parameter of 1 or more' );

  $pqRandomStart = ( $pqMin == PHP_INT_MIN ) ? 1 : $pqMin;
  $pqRandomEnd   = $pqMax;

  if ( $pqInc != 1 )
    $pqRandomSteps = intval ( ( $pqRandomEnd - $pqRandomStart ) / $pqInc );

?>