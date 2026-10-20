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

  // The draws are whole numbers, from the whole numbers inside the window: rand () took a
  // fractional minimal= or maximal= as written and ended the request on PHP's "Implicit
  // conversion from float-string to int loses precision". minimal= and maximal= themselves
  // still bound the terms as given.
  //
  // An end past the integer range is the range's own end, and a draw past minimal= or
  // maximal= as given is turned down by build/one.php, so minimal=1e19, which leaves no whole
  // number in the range, makes no term: cast as it stood, minimal=1e19 ended the request on
  // "The float 1.0E+19 is not representable as an int", and minimal=-1e30 and maximal=1e30,
  // left alone, reached rand () as text and ended it on "rand(): Argument #1 ($min) must be
  // of type int, string given". An end that is an integer is kept as written.

  if ( is_numeric ( $pqRandomStart ) and ! is_int ( $pqRandomStart = $pqRandomStart + 0 ) )
    $pqRandomStart = ceil ( $pqRandomStart ) <= PHP_INT_MIN ? PHP_INT_MIN
                   : ( ceil ( $pqRandomStart ) >= PHP_INT_MAX ? PHP_INT_MAX : (int) ceil ( $pqRandomStart ) );

  if ( is_numeric ( $pqRandomEnd ) and ! is_int ( $pqRandomEnd = $pqRandomEnd + 0 ) )
    $pqRandomEnd = floor ( $pqRandomEnd ) <= PHP_INT_MIN ? PHP_INT_MIN
                 : ( floor ( $pqRandomEnd ) >= PHP_INT_MAX ? PHP_INT_MAX : (int) floor ( $pqRandomEnd ) );

  // An increment below 1 makes no steps - the loop never starts on one - and divided by
  // nothing here: {sequence random, increment=0} ended the request instead of answering
  // nothing, and so did random as a play under a run with that increment.
  //
  // The steps of whole numbers are counted in whole numbers, and those of a window wider than
  // PHP_INT_MAX in two parts that stay inside the range, either side of 0; a fractional step
  // count is kept below 2^63. Divided as one float, the steps of minimal=-9223372036854775807,
  // increment=2 passed the range, and intval ended the request on "The float
  // 9.223372036854776E+18 is not representable as an int".

  if ( $pqInc != 1 and $pqInc >= 1 ) {

    $pqRandomSpan = $pqRandomEnd - $pqRandomStart;
    $pqRandomBy   = $pqInc + 0;

    if ( ! is_int ( $pqRandomBy ) or ! is_int ( $pqRandomStart ) or ! is_int ( $pqRandomEnd ) )
      $pqRandomSteps = intval ( min ( $pqRandomSpan / $pqRandomBy, 9.2233720368547748E18 ) );
    elseif ( is_int ( $pqRandomSpan ) )
      $pqRandomSteps = intdiv ( $pqRandomSpan, $pqRandomBy );
    else
      $pqRandomSteps = intdiv ( $pqRandomEnd, $pqRandomBy ) + intdiv ( -1 - $pqRandomStart, $pqRandomBy )
                     + intdiv ( $pqRandomEnd % $pqRandomBy + ( -1 - $pqRandomStart ) % $pqRandomBy + 1, $pqRandomBy );

  }

?>
