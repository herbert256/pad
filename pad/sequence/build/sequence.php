<?php
 
  if ( $padSeqFlagGo ) {
  
    $padSeqBuildSave = $padSeqBuild;
    $padSeqBuild = 'check';
    $padSeq = include 'sequence/build/call.php';
    $padSeqBuild = $padSeqBuildSave;

        if ( $padSeq === TRUE       ) $padSeq = 1;
    elseif ( $padSeq === FALSE      ) $padSeq = 0;
    elseif ( $padSeq == $padSeqLoop ) $padSeq = 1;
    elseif ( $padSeq <> $padSeqLoop ) $padSeq = 0;
  
  } elseif ( $padSeqBuildType == 'fixed' ) 
  
    $padSeq = $padSeqLoop;
  
  else

    $padSeq = include 'sequence/build/call.php';  
    
?>
