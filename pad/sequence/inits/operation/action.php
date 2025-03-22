<?php 

  $padSeqActionList = $padSeqOperationParms;
  $padSeqActionName = $padSeqOperationValue;

  $padSeqInfo ["start/$padStartType/action"] [] = $padSeqActionName;

  include 'sequence/actions/go.php';

?>