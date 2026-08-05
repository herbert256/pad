<?php

  // Build strategy 'make' for the even sequence, the one pqBuild() settles on since there
  // is no loop.php: an odd candidate is nudged up to the next even number, an even one is
  // returned unchanged, giving 2, 4, 6, 8, ...
  //
  // init.php has normally already put the range on the even numbers, so for a plain build
  // this is the identity. It earns its keep as a play, where {make even} rounds each term
  // of another sequence up to an even value.

  if ( $pqLoop % 2 )
    $pqLoop++;

  return $pqLoop;

?>