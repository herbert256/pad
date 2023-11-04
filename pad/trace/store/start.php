<?php

  $padTraces++;

  foreach ( $GLOBALS as $padK => $padV )
    if ( substr($padK, 0, 8) == 'padTrace' and $padK <> 'padTraces' and $padK <> 'padTracesStore' )
      $padTracesStore [$padTraces] [$padK] = $padV;

?>