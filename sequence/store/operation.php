<?php

  $padSeqSave = $padSeqLoop;

  $padSeqResult [$padIdx] = include '/pad/sequence/build/call.php';

  if     ( $padSeqResult [$padIdx] === FALSE ) unset ( $padSeqResult [$padIdx] ) ;
  elseif ( $padSeqResult [$padIdx] === TRUE  ) $padSeqResult [$padIdx] = $padSeqSave;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqSeq );

?>