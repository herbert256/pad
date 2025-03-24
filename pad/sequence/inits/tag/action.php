<?php

  // {action 'last', 'myseq1', 3}  
  // {action 'myseq1', 'last', 3}  

  padSeqCorrectParm3 ();

  if ( isset ( $padSeqStore [ $padSeqParm2 ] ) ) {
    $padSeqPull       = $padSeqParm2;   
    $padSeqActionName = $padSeqParm1;
  } else {
    $padSeqPull       = $padSeqParm1;   
    $padSeqActionName = $padSeqParm2;
  }

  $padSeqActionParms = $padSeqParm3;

  include 'sequence/inits/go/action.php';

?>