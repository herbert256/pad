<?php
  
  if ( $padSeqPull )
    $padSeqBuild = 'pull';

  if ( ! $padSeqBuild ) 
    $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop' );

?>
