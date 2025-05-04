<?php

if ( $pqParm === TRUE )
    $pqExtra = '';
  else
    $pqExtra = ( $pqParm ) ? "=$pqParm" : $pqExtra = '';

  $pqTmp = padCode ( "{sequence $pqSeq$pqExtra, to=$pqLoop}{\$sequence},{/sequence}" );
  $pqTmp = explode ( ',', $pqTmp );

?>