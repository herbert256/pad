<?php
 
  $padSeqSeqSave  = $padSeqSeq;
  $padSeqParmSave = $padSeqParm;

$padSeqSeq  = $padSeqFlagSeq;
$padSeqParm = $padSeqFlagParm;

  include 'sequence/build/flag/go.php';

  $padSeqSeq  = $padSeqSeqSave;
  $padSeqParm = $padSeqParmSave;

?>