<?php
  
  if ( $padPrmName == 'action' ) {

    $padPrmName  = $padPrmValue;
    $padPrmValue = '';
  
  } elseif ( str_starts_with ( $padPrmName, 'action' ) ) {

    $padPrmName = strtolower ( substr ( $padPrmName, 6, 1 ) ) . substr ( $padPrmName, 7 );

  }

  $padSeqActionName  = $padPrmName; 
  $padSeqActionList  = padExplode ( $padPrmValue, '|' );
  $padSeqActionStore = $padSeqActionList [0] ?? '';
  $padSeqActionCnt   = ( ctype_digit ( $padSeqActionStore ) ) ? $padSeqActionStore : 1;
  
  $padSeqResult = include "/pad/sequence/after/actions/types/$padSeqActionName.php";

  $padSeqDone [] = $padSeqActionName;

  if ( $padSeqActionStore and isset ( $padSeqStore [$padSeqActionStore] ) )
    $padSeqInfo ['actions/double'] [] = $padSeqActionName;
  elseif ( file_exists ( "/pad/sequence/after/actions/single/$padSeqActionName" ) )
    $padSeqInfo ['actions/single'] [] = $padSeqActionName;
  elseif ( file_exists ( "/pad/sequence/after/actions/double/$padSeqActionName" ) )
    $padSeqInfo ['actions/double'] [] = $padSeqActionName;

?>