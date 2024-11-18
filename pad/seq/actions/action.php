<?php
  
  if ( $padPrmName == 'action' ) {

    $padSeqActionList = padExplode ( $padPrmValue, '|' );
    $padSeqActionName = array_shift ( $padSeqActionList );

  } else {

    if ( $padPrmValue === TRUE or ! $padPrmValue )
      $padSeqActionList = [];
    else
      $padSeqActionList = padExplode ( $padPrmValue, '|' );
    
    $padSeqActionName = $padPrmName; 

  }

  $padSeqActionStore = $padSeqActionList [0] ?? '';
  $padSeqActionCnt   = ( ctype_digit ( $padSeqActionStore ) ) ? $padSeqActionStore : 1;

  $padSeqResult = include PAD . "seq/actions/types/$padSeqActionName.php";

  $padSeqDone [] = $padSeqActionName;

  if ( file_exists ( PAD . "seq/actions/single/$padSeqActionName" ) )
    $padSeqInfo ['actions/single'] [] = $padSeqActionName;
  elseif ( file_exists ( PAD . "seq/actions/double/$padSeqActionName" ) )
    $padSeqInfo ['actions/double'] [] = $padSeqActionName;

?>