<?php
  
  if ( $padSeqBuild )
    return;
  
  if ( $padSeqFixed === FALSE )
    $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop');
  else
    $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make');
 
?>