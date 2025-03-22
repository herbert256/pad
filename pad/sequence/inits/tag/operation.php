<?php

  // {action 'reverse', 'myseq', 'parm'}  
  // {one    'sum',     'myseq', 'parm'}  

  $padSeqPull = $padOpt [$pad] [2];  
 
  $padSeqOperationType  = $padTag [$pad];
  $padSeqOperationValue = $padOpt [$pad] [1];
  $padSeqOperationParms = $padOpt [$pad] [3] ?? '' ;

  include 'sequence/inits/operation/operation.php';

?>