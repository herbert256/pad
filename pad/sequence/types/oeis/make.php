<?php

  include_once 'sequence/types/oeis/oeis.php';

  if ( isset ( OEIS [$pqParm] [$pqLoop-1] ) )
    return OEIS [$pqParm] [$pqLoop-1];
  else
    return FALSE;

?>