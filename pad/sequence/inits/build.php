<?php
  
  if ( $padSeqBuildName and $padSeqBuildName !== TRUE )  

    $padSeqBuild = include 'sequence/inits/build/given.php';
  
  elseif ( ! $padSeqBuild ) 

    $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop' );

?>