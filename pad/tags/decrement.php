<?php

  // {decrement $counter} takes one off a counter, starting it at -1 when it did not exist
  // yet.
  //
  // The name is the raw option text with a leading $ stripped, and the counter is a plain
  // global, not a level variable - so it keeps its value across iterations and levels.
  // TRUE is returned, so the tag prints nothing but does not take an @else@ branch.

  $padField = padFieldName ($padOpt [$pad] [0]);

  if ( isset ($GLOBALS[$padField]) )
    $GLOBALS[$padField]--;
  else
    $GLOBALS[$padField] = -1;

  return TRUE;

?>