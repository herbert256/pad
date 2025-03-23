<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqStoreNames;

  $padSeqActionParm = $padSeqActionList [0];
  $padSeqActionCnt  = ( ctype_digit ( $padSeqActionParm ) ) ? $padSeqActionParm : 1;

  if ( $padSeqActionName == 'splice' )
    include 'sequence/store/splice.php';

  $padSeqStore [$padSeqStoreName] = include "sequence/actions/types/$padSeqActionName.php";

  $padSeqNames [] = $padSeqStoreName;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

  $padSeqInfo ['store/actions'] [] = $padSeqActionName;

?>