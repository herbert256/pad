<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqStoreNames;
  $padSeqStoreName  = $padSeqActionList [0];

  if ( $padSeqActionName == 'splice' )
    include '/pad/sequence/store/splice.php';

  $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

?>