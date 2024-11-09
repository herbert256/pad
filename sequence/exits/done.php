<?php

  foreach ( $padSeqDone as $padK => $padV )
    padDone ( $padV );

  foreach ( $padPrm [$pad] as $padK => $padV )
    if ( file_exists ( "/pad/sequence/options/types/$padK.php" ) )
      padDone ( $padK );

?>