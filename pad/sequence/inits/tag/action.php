<?php

  // {action 'sum', 'myseq1'}  
  // {action 'sum', 'last'}  

  // {action 'last', 'myseq1', 3}  
  // {action 'myseq1', 'last', 3}  

  // {action 'merge', 'myseq1', 'myseq2|myseq3'}  
  // {action 'myseq1', 'merge', 'myseq2|myseq3'}  

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