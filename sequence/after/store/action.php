<?php

  $padSeqActionName = $padSeqStoreAction;
  $padSeqActionList = $padSeqStoreNames;

  if ( $padSeqActionName == 'splice' )
    include '/pad/sequence/after/store/splice.php';

  $padSeqStore [$padSeqStoreName] = include "/pad/sequence/after/actions/types/$padSeqActionName.php";

  $padSeqDone [] = 'store'. ucfirst ( $padSeqActionName );

  $padSeqInfo ['store/actions'] [] = $padSeqActionName;

?>