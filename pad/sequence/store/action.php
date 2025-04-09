<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqPushNames;

  $padSeqActionParm = $padSeqActionList [0];
  $padSeqActionCnt  = ( ctype_digit ( $padSeqActionParm ) ) ? $padSeqActionParm : 1;

  if ( $padSeqActionName == 'splice' )
    include 'sequence/store/splice.php';

  $padSeqStore [$padSeqPushName] = include "sequence/actions/types/$padSeqActionName.php";

  $padSeqNames [] = $padSeqPushName;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

  $padSeqInfo ['store/actions'] [] = $padSeqActionName;

?>