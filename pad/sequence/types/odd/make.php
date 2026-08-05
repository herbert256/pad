<?php

  // Make build for odd, the strategy pqBuild() picks for this type: nudges an even
  // candidate up to the next odd number, so every loop value maps onto a term rather than
  // being tested. In practice init.php has already forced the candidates odd, so the bump
  // only fires when something else moved the range.

  if ( ! ($pqLoop % 2) )
    $pqLoop++;

  return $pqLoop;

?>