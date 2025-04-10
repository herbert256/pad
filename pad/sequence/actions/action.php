<?php

  if ( $padPrmValue === TRUE )
    $padPrmValue = '';

  $padSeqActionList = padExplode ( $padPrmValue, '|' );

  if ( $padPrmName == 'action' ) $padSeqActionName = array_shift ( $padSeqActionList );
  else                           $padSeqActionName = $padPrmName;     

  $padSeqActionParm = $padSeqActionList [0] ?? '';

  include 'sequence/actions/go.php';
  
?>