<?php

  // Range setup for divide, run before generation: its parameter is a number
  // the type does arithmetic with, so one that is not is reported here - once for the
  // build rather than once per candidate - instead of ending the request inside the type.

  include PQ . 'inits/number.php';

  // A 0 spelled '0.0', '-0' or '00' is the zero it is: ! $pqParm let those strings through, and
  // {sequence divide='0.0'} ended the request on "Division by zero".

  if ( is_numeric ( $pqParm ) and $pqParm == 0 )
    padError ( 'The divide sequence cannot divide by zero' );

?>
