<?php

  if ( $pqParm === TRUE )
    $pqExtra = '';
  else
    $pqExtra = ( $pqParm ) ? "=$pqParm" : $pqExtra = '';

  $pqTmp3 = "{sequence $pqSeq$pqExtra, sole=$pqLoop}{\$sequence},{/sequence}";
  $pqTmp2 = padCode ( $pqTmp3 );
  $pqTmp  = explode ( ',', $pqTmp2 );

  return  ( isset ( $pqTmp [0]) ) ? $pqTmp [0] : FALSE;
  
?>