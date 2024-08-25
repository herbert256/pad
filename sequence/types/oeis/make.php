<?php

  include_once '/pad/sequence/types/oeis/oeis.php';

  if ( isset ( OEIS [$padSeqOeis] [$padSeqLoop-1] ) )
    return OEIS [$padSeqOeis] [$padSeqLoop-1];
  else
    return FALSE;

?>