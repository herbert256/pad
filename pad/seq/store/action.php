<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqStoreNames;

  if ( $padSeqActionName == 'splice' )
    include PAD . 'seq/store/splice.php';

  $padSeqStore [$padSeqStoreName] = include PAD . "seq/actions/types/$padSeqActionName.php";

  $padSeqNames [] = $padSeqStoreName;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

  $padSeqInfo ['store/actions'] [] = $padSeqActionName;

?>