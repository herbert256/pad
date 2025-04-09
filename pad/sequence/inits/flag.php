<?php

  $padSeqFlagGo = FALSE;
  
  if ( ! $padSeqFlag ) 
    return;
    
  foreach ( $padSeqPlays as $padSeqPlay )
    if ( $padSeqPlay ['padSeqPlay'] == 'flag' )
      return;
      
  if ( ! file_exists ( "sequence/types/$padSeqSeq" ) )
    return;
    
  $padSeqFlagGo = TRUE;

  $padSeqBuildSave = $padSeqBuild;
  $padSeqBuild = 'check';
  include 'sequence/build/include.php';
  $padSeqBuild = $padSeqBuildSave;

?>
