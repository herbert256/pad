<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqStoreNames;

  if ( $padSeqActionName == 'splice' )
    include '/pad/sequence/store/splice.php';

  $padSeqStore [$padSeqStoreName] = include "/pad/sequence/actions/types/$padSeqActionName.php";

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

?>