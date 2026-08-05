<?php

  // Fires from level/start.php once a level is fully set up - parameters parsed, data, base
  // and options resolved - and immediately before its occurrences are iterated.
  //
  // Writes the trace report's level block, and for the xref records the tag under its type
  // (with any @property suffix stripped off) plus, for plain 'tag' types, an entry in the
  // properties index.

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