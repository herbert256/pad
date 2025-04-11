<?php

  $padSeqParmSave  = $padSeqParm;
  $padSeqIncSave   = $padSeqInc;
  $padSeqStartSave = $padSeqStart;
  $padSeqEndSave   = $padSeqEnd;

  $padSeqParm = $padPrmValue;

  if ( $padSeqParm and isset ( $padSeqStore [$padSeqParm] ) )
    $padSeqParm = reset ( $padSeqStore [$padSeqParm] );

  include "sequence/types/$padSeqSeq/init.php";

  $padSeqParm  = $padSeqParmSave;
  $padSeqInc   = $padSeqIncSave;
  $padSeqStart = $padSeqStartSave;
  $padSeqEnd   = $padSeqEndSave;

?>