<?php
 
  $pqSeqSave   = $pqSeq;
  $pqBuildSave = $pqBuild;
  $pqParmSave  = $pqParm;
  $pqLoopSave  = $pqLoop;

  include 'sequence/plays/go.php';

  $pqSeq   = $pqSeqSave;
  $pqBuild = $pqBuildSave;
  $pqLoop  = $pqLoopSave;
  $pqParm  = $pqParmSave;

?>