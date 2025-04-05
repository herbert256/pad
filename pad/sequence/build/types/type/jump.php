<?php

  $padSeqBuildType = 'jump';
  
  include 'sequence/build/randomly/init.php';

  $padSeqLoop = $padSeqStart;

  while ( $padSeqLoop <= $padSeqEnd )
    if ( ! include 'sequence/build/one.php')
      break;

?>