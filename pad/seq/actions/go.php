<?php

  $padSeqActionStore = $padSeqActionList [0] ?? '';
  $padSeqActionCnt   = ( ctype_digit ( $padSeqActionStore ) ) ? $padSeqActionStore : 1;

  $padSeqResult = include "seq/actions/types/$padSeqActionName.php";

  $padSeqDone [] = $padSeqActionName;

  if ( file_exists ( "seq/actions/single/$padSeqActionName" ) )
    $padSeqInfo ['actions/single'] [] = $padSeqActionName;
  elseif ( file_exists ( "seq/actions/double/$padSeqActionName" ) )
    $padSeqInfo ['actions/double'] [] = $padSeqActionName;

?>