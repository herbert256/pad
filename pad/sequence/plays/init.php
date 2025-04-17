<?php

  $pqParmSave  = $pqParm;
  $pqIncSave   = $pqInc;
  $pqStartSave = $pqStart;
  $pqEndSave   = $pqEnd;

  $pqParm = $padPrmValue;

  if ( $pqParm and isset ( $pqStore [$pqParm] ) )
    $pqParm = reset ( $pqStore [$pqParm] );

  include "sequence/types/$pqSeq/init.php";

  $pqParm  = $pqParmSave;
  $pqInc   = $pqIncSave;
  $pqStart = $pqStartSave;
  $pqEnd   = $pqEndSave;

?>