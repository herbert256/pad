<?php
 
  if ( in_array ( $padSeqBuild, ['keep', 'remove', 'flag'] ) ) {

    $padSeqBuildSave = $padSeqBuild;
    $padSeqPlay      = $padSeqBuild;
    $padSeqBuild     = 'check';

    include "sequence/plays/one.php";

    $padSeqBuild = $padSeqBuildSave;
    
  } elseif ( $padSeqBuildType == 'fixed' ) 
  
    $padSeq = $padSeqLoop;
  
  else

    $padSeq = include 'sequence/build/call.php';  
    
?>