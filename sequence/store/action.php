<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqStoreNames;

  if ( $padSeqActionName == 'splice' )
    include '/pad/sequence/store/splice.php';

  $padSeqStore [$padSeqStoreName] = include "/pad/sequence/actions/types/$padSeqActionName.php";

  $padSeqNames [] = $padSeqStoreName;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

  $padSeqInfo ['store/actions'] [] = $padSeqActionName;

?>