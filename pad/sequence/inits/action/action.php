<?php

  $padSeqPull = '';
  
  include "sequence/inits/$padSeqInitType/action.php";

  if ( ! $padSeqPull and $padSeqPullName )
    include "sequence/inits/pull/action.php";

  if ( $padSeqPull )
    return include 'sequence/inits/go/action.php';

  if ( file_exists ( "sequence/actions/types/$padSeqTag.php") ) { 
    $padSeqActionAfterName = $padSeqTag;
    $padSeqActionAfterParm = $padSeqParm;
  } elseif ( file_exists ( "sequence/actions/types/$padSeqPrefix.php") ) { 
    $padSeqActionAfterName = $padSeqPrefix;
    $padSeqActionAfterParm = $padSeqParm;
  } elseif ( file_exists ( "sequence/actions/types/$padSeqFirst.php") ) { 
    $padSeqActionAfterName = $padSeqFirst;
    $padSeqActionAfterParm = $padSeqParm;
    $padSeqDone []         = $padSeqFirst;
  }

?>