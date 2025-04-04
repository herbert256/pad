<?php

  $padSeqBuildType = 'jump';
  
  $padSeqLoop = $padSeqStart;

  while ( $padSeqLoop <= $padSeqEnd )
    if ( ! include 'sequence/build/one.php')
      break;

?>