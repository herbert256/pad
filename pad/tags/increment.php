<?php

  // The {increment $counter} tag: adds one to a global variable, creating it as 1 when it did
  // not exist yet.
  //
  // The name comes from $padOpt [$pad] [0], the raw text following the tag name, not from the
  // evaluated parameter - padFieldName just strips a leading $ - so the variable is addressed
  // by name and never read as a value. tags/decrement.php is the mirror image.
  //
  // The name must be an application variable (padValidVar): {increment $padPage} counted
  // on the engine's own state.

  $padField = padFieldName ( $padOpt [$pad] [0] );

  if ( ! padValidVar ( $padField ) ) {
    padError ( "{increment} counts an application variable, not '$padField'" );
    return FALSE;
  }

  // An empty value counts as one not there yet - a request value ?n= or a {set $n = ''}
  // - and a number counts on. PHP's own ++ raised a deprecation on an empty or worded
  // string, and threw on an array, and either ended the request with a 500; a value that
  // is no number is now named.

  $padCounter = $GLOBALS [$padField] ?? NULL;

  if ( $padCounter === NULL or $padCounter === '' )
    $GLOBALS [$padField] = 1;
  elseif ( is_numeric ( $padCounter ) )
    $GLOBALS [$padField] = $padCounter + 1;
  else {
    padError ( "{increment} counts a number, and \$$padField is not one" );
    return FALSE;
  }

  return TRUE;

?>