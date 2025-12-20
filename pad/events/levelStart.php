<?php

  global $padInfoTrace, $padInfoXref;

  if ( $padInfoTrace )
    include PAD . 'info/types/trace/level/info.php';

  if (  $padInfoXref ) {

    if ( str_contains($padTag [$pad], '@'))
      $padInfoTmp = strstr ( $padTag [$pad] , "@", true ) ;
    else
      $padInfoTmp = $padTag [$pad] ;

    padInfoXref ( 'tag', $padType [$pad], $padInfoTmp );

    if ( $padType [$pad] == 'tag' )
      padInfoXref ( 'properties', $padInfoTmp );

  }

?>
