<?php

  $padSeqActionCnt  = ( ctype_digit ( $padSeqActionParm ) ) ? $padSeqActionParm : 1;

  $padSeqResult = include "sequence/actions/types/$padSeqActionName.php";

  $padSeqDone  [] = $padSeqActionName;
  $padSeqNames [] = $padSeqActionName;

  if ( file_exists ( "sequence/actions/single/$padSeqActionName" ) )
    $padSeqInfo ['actions/single'] [] = $padSeqActionName;
  elseif ( file_exists ( "sequence/actions/double/$padSeqActionName" ) )
    $padSeqInfo ['actions/double'] [] = $padSeqActionName;

?>