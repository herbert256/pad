<?php
  
  $padSeqActionName = $padPrmName; 
  $padSeqActionList = padExplode ( $padPrmValue, '|' );
  $padSeqActionCnt  = ( ctype_digit ( $padSeqActionList [0]) ) ? $padSeqActionList [0] : 1;
  
  $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

  $padSeqDone [] = $padSeqActionName;

?>