<?php

  // {decrement $counter} takes one off a counter, starting it at -1 when it did not exist
  // yet.
  //
  // The name is the raw option text with a leading $ stripped, and the counter is a plain
  // global, not a level variable - so it keeps its value across iterations and levels.
  // TRUE is returned, so the tag prints nothing but does not take an @else@ branch.
  //
  // The name must be an application variable (padValidVar): {decrement $padPage} counted
  // on the engine's own state.

  $padField = padFieldName ($padOpt [$pad] [0]);

  if ( ! padValidVar ( $padField ) ) {
    padError ( "{decrement} counts an application variable, not '$padField'" );
    return FALSE;
  }

  // An empty value counts as one not there yet - a request value ?n= or a {set $n = ''}
  // - and a number counts on. PHP's own -- raised a deprecation on an empty or worded
  // string, and threw on an array, and either ended the request with a 500; a value that
  // is no number is now named.

  $padCounter = $GLOBALS [$padField] ?? NULL;

  if ( $padCounter === NULL or $padCounter === '' )
    $GLOBALS [$padField] = -1;
  elseif ( is_numeric ( $padCounter ) )
    $GLOBALS [$padField] = $padCounter - 1;
  else {
    padError ( "{decrement} counts a number, and \$$padField is not one" );
    return FALSE;
  }

  return TRUE;

?>