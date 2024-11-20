<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqStoreNames;

  if ( $padSeqActionName == 'splice' )
    include 'seq/store/splice.php';

  $padSeqStore [$padSeqStoreName] = include "seq/actions/types/$padSeqActionName.php";

  $padSeqNames [] = $padSeqStoreName;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

  $padSeqInfo ['store/actions'] [] = $padSeqActionName;

?>