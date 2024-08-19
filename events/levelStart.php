<?php

  if ( $GLOBALS ['padInfoTrace'] )
    include '/pad/info/types/trace/level/info.php';   

  if ( $GLOBALS ['padInfoXref'] or $GLOBALS ['padInfoXapp'] ) {

    if ( str_contains($padTag [$pad], '@'))  
      $padInfoTmp = strstr ( $padTag [$pad] , "@", true ) ;
    else
      $padInfoTmp = $padTag [$pad] ;

    padInfoXapp ( 'tag', $padType [$pad], $padInfoTmp );

    if ( $padType [$pad] == 'tag' )
      padInfoXapp ( 'properties', $padInfoTmp );

  }
  
?>