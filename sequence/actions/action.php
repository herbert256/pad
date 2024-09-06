<?php
  
  $padSeqActionName  = $padPrmName; 
  $padSeqActionList  = padExplode ( $padPrmValue, '|' );
  $padSeqActionStore = $padSeqActionList [0] ?? '';
  $padSeqActionCnt   = ( ctype_digit ( $padSeqActionStore ) ) ? $padSeqActionStore : 1;
  
  $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

  $padSeqDone [] = $padSeqActionName;

  if ( $padSeqActionStore and isset ( $padSeqStore [$padSeqActionStore] ) )
    $padSeqInfo ['actions/double'] [] = $padSeqActionName;
  elseif ( file_exists ( "/pad/sequence/actions/single/$padSeqActionName" ) )
    $padSeqInfo ['actions/single'] [] = $padSeqActionName;
  elseif ( file_exists ( "/pad/sequence/actions/double/$padSeqActionName" ) )
    $padSeqInfo ['actions/double'] [] = $padSeqActionName;

?>