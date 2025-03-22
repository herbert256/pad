<?php
 
  // {action:reverse 'myseq', 'parm' } 
  // {reverse:myseq  'parm' }          

  if     ( $padPrefix[$pad] and file_exists ("sequence/one/types/$padPrefix[$pad].php")     ) $padSeqTmp = 'one';
  elseif ( $padPrefix[$pad] and file_exists ("sequence/actions/types/$padPrefix[$pad].php") ) $padSeqTmp = 'action';
  else                                                                                        $padSeqTmp = '';

  if ( $padSeqTmp == 'one' or $padSeqTmp == 'action' ) {

    $padSeqPull = $padTag [$pad];  

    $padSeqOperationType  = $padSeqTmp;
    $padSeqOperationValue = $padPrefix [$pad];
    $padSeqOperationParms = $padOpt    [$pad] [1] ?? '';

    $padSeqInfo ["start/prefix/$padSeqTmp"] [] = $padPrefix [$pad];

  } else {

    $padSeqPull = $padParm;  

    $padSeqOperationType  = $padType [$pad];
    $padSeqOperationValue = $padTag  [$pad];
    $padSeqOperationParms = $padOpt  [$pad] [2] ?? '';

   }

  include 'sequence/inits/operation/operation.php';
 
?>