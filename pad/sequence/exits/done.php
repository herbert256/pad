<?php

  foreach ( $pqDone as $padK => $padV )
    if ( ! is_array ( $padV ) )
      padDone ( $padV );

  foreach ( $padPrm [$pad] as $padK => $padV )
    if ( file_exists ( "sequence/options/types/$padK.php" ) )
      if ( ! is_array ( $padV ) )
        padDone ( $padK );

?>