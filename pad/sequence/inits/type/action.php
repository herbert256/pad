<?php
 
  // {reverse:myseq  'parm' }          
  // {myseq:reverse  'parm' }          
  // {action:reverse 'myseq', 'parm' } 


  if ( $padTypeReverse ) {

    $padSeqPull = $padPrefix[$pad];  

    $padSeqActionName  = $padSeqFirst;
    $padSeqActionParms = $padSeqSecond;

    $padSeqInfo ["start/reverse"] [] = $padSeqActionName;

  } elseif ( $padPrefix[$pad] and file_exists ("sequence/actions/types/$padPrefix[$pad].php") ) {

    $padSeqPull = $padSeqFirst;  

    $padSeqActionName  = $padPrefix [$pad];
    $padSeqActionParms = $padSeqSecond;

    $padSeqInfo ["start/prefix"] [] = $padSeqActionName;

  } else {

    $padSeqPull = $padSeqFirst;  

    $padSeqActionName  = $padTag  [$pad];
    $padSeqActionParms = $padOpt  [$pad] [2] ?? '';

  }

  include 'sequence/inits/go/action.php';
 
?>