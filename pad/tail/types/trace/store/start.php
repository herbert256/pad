<?php

  if ( ! isset ( $padTraces ) )
    $padTraces = 0;

  $padTraces++;

  foreach ( $GLOBALS as $padK => $padV )
    if ( substr($padK, 0, 8) == 'padTrace' )
      if ( $padK <> 'padTraces' and $padK <> 'padTracesStore' and $padK <> 'padTraceLine' )
        $padTracesStore [$padTraces] [$padK] = $padV;

?>