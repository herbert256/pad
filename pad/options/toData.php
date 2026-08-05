<?php

  // Implements toData="name": stores the level's data array in $padDataStore under that name,
  // to be read back with {data:name} or the data option. End-phase option.
  //
  // A tag with neither a pair nor content stores its data as it stands and leaves the result
  // alone; otherwise what gets stored is the walked data, or the plain data when the walk never
  // started, and the result is blanked so the storing prints nothing.

  $padStoreName = $padPrm [$pad] ['toData'];

  if ( !$padPair and !$padContent and !padIsDefaultData($padData [$pad]) ) {
    $padDataStore [$padStoreName] = $padData [$pad];
    return;
  }

  if ( $padWalk  [$pad] != 'start' )
    $padDataStore [$padStoreName] = $padWalkData [$pad];
  else
    $padDataStore [$padStoreName] = $padData [$pad];

  $padResult [$pad] = '';

?>