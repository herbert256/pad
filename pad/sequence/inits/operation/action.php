<?php 

  $padSeqActionList = $padSeqOperationParms;
  $padSeqActionName = $padSeqOperationValue;

  $padSeqInfo ['start-action'] [] = $padSeqActionName;

  include 'sequence/actions/go.php';

?>