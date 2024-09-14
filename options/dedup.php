<?php

  $padDedup = [];

  foreach ( $padData [$pad] as $padK => $padV)
    if ( is_array ($padV) and count($padV) == 1 and isset ( $padV [$padName [$pad]] ) )
      $padDedup [ $padV [$padName [$pad]] ] = [ $padName [$pad] => $padV [$padName [$pad]] ] ;

  if ( count($padDedup))
    $padData [$pad] = $padDedup;


?>