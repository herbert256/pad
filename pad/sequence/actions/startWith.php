<?php

  if ( $padPrmName == 'saction' )
    return;
  
  if ( $padPrmValue === TRUE or ! $padPrmValue )
    $padSeqActionList = [];
  else
    $padSeqActionList = padExplode ( $padPrmValue, '|' );
  
  $padSeqActionName = $padPrmName; 

  include 'sequence/actions/go.php';
  
?>