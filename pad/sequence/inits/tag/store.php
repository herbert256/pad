<?php

  if ( $padSeqFirst and isset ( $padSeqStore [$padSeqFirst] ) )
  
    $padSeqPull = $padSeqFirst;  
  
  elseif ( $padSeqPullName and isset ( $padSeqStore [$padSeqPullName] ) ) {
  
    $padSeqPull     = $padSeqPullName;
    $padSeqPullName = '';
  
  } elseif ( $padSeqParm and isset ( $padSeqStore [$padSeqParm] ) ) 
  
    $padSeqPull = $padSeqParm;
  
  else

    padError ( "No store found" );

  include 'sequence/inits/go/store.php';

?>