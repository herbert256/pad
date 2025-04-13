<?php

  if ( $padPrmValue === TRUE )
    $padPrmValue = '';

  $padSeqActionList = padExplode ( $padPrmValue, '|' );

  if ( $padPrmName == 'action' ) $padSeqAction = array_shift ( $padSeqActionList );
  else                           $padSeqAction = $padPrmName;     

  $padSeqActionParm = $padSeqActionList [0] ?? '';

  include 'sequence/actions/go.php';
  
?>