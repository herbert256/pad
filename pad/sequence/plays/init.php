<?php

  $padSeqIncSave   = $padSeqInc;
  $padSeqParmSave  = $padSeqParm;
  $padSeqStartSave = $padSeqStart;

  $padSeqParm = $padPrmValue;

  if ( isset ( $padSeqStore [$padSeqParm] ) )
    $padSeqParm = reset ( $padSeqStore [$padSeqParm] );

  include "sequence/types/$padSeqSeq/init.php";

  $padSeqParm  = $padSeqParmSave;
  $padSeqInc   = $padSeqIncSave;
  $padSeqStart = $padSeqStartSave;

?>