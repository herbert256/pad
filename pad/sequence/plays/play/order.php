<?php

  if ( defined ( "PAD$pqSeq" ) and isset ( constant ( "PAD$pqSeq" ) [$pqLoop-1] ) )
    return constant ( "PAD$pqSeq" ) [$pqLoop-1];

  $pqTmp = pqArray ( $pqSeq, $pqParm, "sole=$pqLoop" );

  return  ( isset ( $pqTmp [0]) ) ? $pqTmp [0] : FALSE;

?>