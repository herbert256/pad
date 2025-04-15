<?php
 
  if ( in_array ( $padSeqBuild, ['keep', 'remove', 'flag'] ) ) {

    $padSeqBuildSave = $padSeqBuild;
    $padSeqPlay      = $padSeqBuild;
    $padSeqBuild     = 'check';

    include "sequence/plays/one.php";

    $padSeqBuild = $padSeqBuildSave;
    
  } elseif ( padSeqStore ( $padSeqBuild ) ) 
  
    $padSeq = $padSeqLoop;
  
  else

    $padSeq = include 'sequence/build/call.php';  
    
?>