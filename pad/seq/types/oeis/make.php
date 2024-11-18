<?php

  include_once PAD . 'seq/types/oeis/oeis.php';

  if ( isset ( OEIS [$padSeqParm] [$padSeqLoop-1] ) )
    return OEIS [$padSeqParm] [$padSeqLoop-1];
  else
    return FALSE;

?>