<?php

  if ( $padSeqFirst and isset ( $padSeqStore [$padSeqFirst] ) )
  
    $padSeqPull = $padSeqFirst;  
  
  elseif ( $padSeqPullName and isset ( $padSeqStore [$padSeqPullName] ) ) {
  
    $padSeqPull     = $padSeqPullName;
    $padSeqPullName = '';
  
  } elseif ( $padSeqParm and isset ( $padSeqStore [$padSeqParm] ) ) 
  
    $padSeqPull = $padSeqParm;

  elseif ( $padLastPush )

    $padSeqPull = $padLastPush;

  include 'sequence/inits/go/store.php';

?>