<?php

  $padSeqTemp = include "sequence/types/$padSeqSeq/fixed.php";

  if ( isset ( $padSeqTemp [$padSeqLoop-1] ) )
    return $padSeqTemp [$padSeqLoop-1];
  else
    return FALSE;

?>