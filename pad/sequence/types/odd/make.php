<?php

  // Make build for odd, the strategy pqBuild() picks for this type: nudges an even
  // candidate up to the next odd number, so every loop value maps onto a term rather than
  // being tested. In practice init.php has already forced the candidates odd, so the bump
  // only fires when something else moved the range.

  // A value that is no whole number has no term here - a word in the list a make play is
  // handed ended the request on the arithmetic, and a fraction on PHP's deprecation of it
  // as an integer.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  if ( ! ($pqLoop % 2) )
    $pqLoop++;

  return $pqLoop;

?>
