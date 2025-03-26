<?php

  // {action 'last', 'myseq1', 3}  
  // {action 'myseq1', 'last', 3}  

  if ( isset ( $padSeqStore [ $padSeqPrm2 ] ) ) {
    $padSeqPull       = $padSeqPrm2;   
    $padSeqActionName = $padSeqPrm1;
  } else {
    $padSeqPull       = $padSeqPrm1;   
    $padSeqActionName = $padSeqPrm2;
  }

  $padSeqActionParms = $padSeqPrm3;

  include 'sequence/inits/go/action.php';

?>