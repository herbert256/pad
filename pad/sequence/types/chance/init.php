<?php

  // Range setup for chance, run before generation: its parameter is either a percentage,
  // '25%', or the N of a one-in-N draw, so it is checked here rather than through
  // inits/number.php - a percentage is not a number and that check would refuse it.
  //
  // A range or a store name is not resolved until each candidate is built, so neither is a
  // number yet and both are loop.php's to read.

  if ( $pqParm === '' or $pqParm === TRUE or $pqParm === NULL )
    return;

  if ( str_contains ( $pqParm, '%' ) or str_contains ( $pqParm, '..' ) )
    return;

  if ( isset ( $pqStore [$pqParm] ) )
    return;

  if ( ! is_numeric ( $pqParm ) )
    return padError ( "The chance sequence needs a number or a percentage as its parameter, not '$pqParm'" );

  if ( $pqParm < 1 )
    padError ( 'The chance sequence needs a parameter of 1 or more' );

?>