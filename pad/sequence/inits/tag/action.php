<?php

  // {action 'last', 'myseq1', 3}  
  // {action 'myseq1', 'last', 3}  

  padSeqCorrectParm3 ();

  if ( isset ( $padSeqStore [ $padPrm2 ] ) ) {
    $padSeqPull       = $padPrm2;   
    $padSeqActionName = $padPrm1;
  } else {
    $padSeqPull       = $padPrm1;   
    $padSeqActionName = $padPrm2;
  }

  $padSeqActionParms = $padPrm3;

  include 'sequence/inits/go/action.php';

?>