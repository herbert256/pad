<?php

  $pqParmSave  = $pqParm;
  $pqIncSave   = $pqInc;
  $pqFromSave = $pqFrom;
  $pqToSave   = $pqTo;

  $pqParm = $padPrmValue;

  if ( $pqParm and isset ( $pqStore [$pqParm] ) )
    $pqParm = reset ( $pqStore [$pqParm] );

  include "sequence/types/$pqSeq/init.php";

  $pqParm  = $pqParmSave;
  $pqInc   = $pqIncSave;
  $pqFrom = $pqFromSave;
  $pqTo   = $pqToSave;

?>