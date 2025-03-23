<?php

  // {action 'last', 'myseq1', 3}  
  // {action 'myseq1', 'last', 3}  

  if ( isset ( $padSeqStore [ $padOpt [$pad] [2] ] ) ) {
    $padSeqPull       = $padOpt [$pad] [2];   
    $padSeqActionName = $padOpt [$pad] [1];
  } else {
    $padSeqPull       = $padOpt [$pad] [1];   
    $padSeqActionName = $padOpt [$pad] [2];
  }

  $padSeqActionParms = $padOpt [$pad] [3] ?? '' ;

  include 'sequence/inits/go/action.php';

?>