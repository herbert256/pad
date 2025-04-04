<?php
 
  if ( $padSeqFlag and ! $padSeqFlagSeq )
    include 'sequence/build/flag/check.php';
  else
    if     ( $padSeqBuild == 'fixed' ) $padSeq = $padSeqLoop;
    elseif ( $padSeqBuild == 'store' ) $padSeq = $padSeqLoop;
    elseif ( $padSeqBuild == 'start' ) $padSeq = $padSeqLoop;
    else                               $padSeq = include 'sequence/build/call.php';

?>