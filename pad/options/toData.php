<?php

  // Implements toData="name": stores the level's data array in $padDataStore under that name,
  // to be read back with {data:name} or the data option. End-phase option.
  //
  // A tag with neither a pair nor content stores its data as it stands and leaves the result
  // alone; otherwise what gets stored is the walked data, or the plain data when the walk never
  // started, and the text is blanked so the storing prints nothing - on $padContent, because
  // the walker copies the result back over it after the phase, which silently undid the
  // direct $padResult blanking this used to do. After an @end@ coda, which renders on a
  // stand-in row, the rows are where start_end/end2.php put them aside.

  $padStoreName = $padPrm [$pad] ['toData'];

  $padStoreText ??= $padContent;

  // A level on its @else@ branch has no rows - its one occurrence is the stand-in that renders
  // the branch, and stored, a later {pad data='x'} rendered a row of an empty list.

  if ( $padElse [$pad] ) {
    $padDataStore [$padStoreName] = [];
    $padContent = '';
    return;
  }

  if ( ! $padPair [$pad] and ! $padStoreText and !padIsDefaultData($padData [$pad]) ) {
    $padDataStore [$padStoreName] = $padData [$pad];
    return;
  }

  if ( $padWalk  [$pad] != 'start' )
    $padDataStore [$padStoreName] = $padWalkData [$pad];
  elseif ( isset ( $padSectionRows [$pad] ) )
    $padDataStore [$padStoreName] = $padStartData [$pad];
  else
    $padDataStore [$padStoreName] = $padData [$pad];

  $padContent = '';

?>
