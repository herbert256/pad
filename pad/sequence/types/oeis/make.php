<?php

  include_once PT . 'oeis/oeis.php';

  if ( isset ( OEIS [$pqParm] [$pqLoop-1] ) )
    return OEIS [$pqParm] [$pqLoop-1];
  else
    return FALSE;

?>
