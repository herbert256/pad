<?php

  foreach ( $padData [$pad] as $padK => $padV )   
    if ( isset ( $pqPlaysHit [$padK] ) )
      $padData [$pad] [$padK] = array_merge ( $padV, $pqPlaysHit [$padK] );

?>