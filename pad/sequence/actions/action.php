<?php

  if ( $padPrmValue === TRUE )
    $padPrmValue = '';

  $padSeqActionList = padExplode ( $padPrmValue, '|' );

  if     ( $padPrmName == 'action'                   ) $padSeqActionName = array_shift ( $padSeqActionList );
  elseif ( str_starts_with ( $padPrmName, 'action' ) ) $padSeqActionName = strtolower ( substr ( $padPrmName, 6 ) ); 
  else                                                 $padSeqActionName = $padPrmName;     

  $padSeqActionParm = $padSeqActionList [0] ?? '';

  include 'sequence/actions/go.php';
  
?>