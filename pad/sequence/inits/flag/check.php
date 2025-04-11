<?php

  if ( ! file_exists ( "sequence/types/$padSeqSeq" ) )
    return;

  $padSeqFlagGo = TRUE;

  $padSeqBuildSave = $padSeqBuild;
  $padSeqBuild     = 'check';

  include 'sequence/build/include.php';

  $padSeqBuild = $padSeqBuildSave;

?>