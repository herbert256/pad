<?php

  if ( file_exists ( "sequence/types/$padSeqSeq" ) )  {
    $padSeqBuild = $padSeqBuildName;
    return;
  }

  foreach ( $padSeqPlays as $padK => $padV ) {
      $padSeqPlays [$padK] ['padSeqBuild'] = $padSeqBuildName;
      return;
    }

?>