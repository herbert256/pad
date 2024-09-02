<?php

  $padSeqActionName  = $padSeqStoreAction;
  $padSeqActionValue = $padSeqStoreName;
  $padSeqActionList  = padExplode ( $padSeqActionValue, '|' );

  if ( $padSeqActionValue === TRUE or ! ctype_digit($padSeqActionValue) )
    $padSeqActionCnt = 1;
  else
    $padSeqActionCnt = $padSeqActionValue;

  if ( $padSeqActionName == 'splice' )
    include '/pad/sequence/store/splice.php';

  if ( $padSeqActionName <> 'replace' )
    $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );;

?>