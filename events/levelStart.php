<?php

  if ( $GLOBALS ['padInfoTrace'] )
    include '/pad/info/types/trace/level/info.php';   

  if ( $GLOBALS ['padInfoXref'] or $GLOBALS ['padInfoXapp'] ) {
    padInfoXapp ( 'tag', $padType [$pad], $padTag [$pad] );
    if ( $padType [$pad] == 'tag' )
      padInfoXapp ( 'properties', $padTag [$pad] );
  }
  
?>