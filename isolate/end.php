<?php

  foreach ($GLOBALS as $padK => $padV)
    if ( padValidStore ($padK) )
      unset ( $GLOBALS[$padK] );

  foreach ( $padIsolate [$pad] as $padK => $padV )
    $GLOBALS [$padK] = $padIsolate [$pad] [$padK];

  unset ( $padIsolate [$pad] );
  
?>