<?php
 
  if     ( $padSeqBuildType == 'fixed' ) $padSeq = $padSeqLoop;
  else                                   $padSeq = include 'sequence/build/call.php';

?>