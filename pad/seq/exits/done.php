<?php

  foreach ( $padSeqDone as $padK => $padV )
    padDone ( $padV );

  foreach ( $padPrm [$pad] as $padK => $padV )
    if ( file_exists ( "seq/options/types/$padK.php" ) )
      padDone ( $padK );

?>