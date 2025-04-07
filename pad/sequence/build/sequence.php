<?php
 
  if     ( $padSeqBuild == 'fixed' ) $padSeq = $padSeqLoop;
  elseif ( $padSeqBuild == 'store' ) $padSeq = $padSeqLoop;
  elseif ( $padSeqBuild == 'start' ) $padSeq = $padSeqLoop;
  else                               $padSeq = include 'sequence/build/call.php';

?>