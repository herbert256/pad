<?php

  if ( $GLOBALS ['padInfoTrace'] )
    include PAD . 'info/types/trace/level/info.php';

  if (  $GLOBALS ['padInfoXref'] ) {

    if ( str_contains($padTag [$pad], '@'))
      $padInfoTmp = strstr ( $padTag [$pad] , "@", true ) ;
    else
      $padInfoTmp = $padTag [$pad] ;

    padInfoXref ( 'tag', $padType [$pad], $padInfoTmp );

    if ( $padType [$pad] == 'tag' )
      padInfoXref ( 'properties', $padInfoTmp );

  }

?>