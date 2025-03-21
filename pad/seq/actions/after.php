<?php
  
  if ( $padPrmValue === TRUE or ! $padPrmValue )
    $padSeqActionList = [];
  else
    $padSeqActionList = padExplode ( $padPrmValue, '|' );
  
  $padSeqActionName = $padPrmName; 

  include 'seq/actions/go.php';
  
?>