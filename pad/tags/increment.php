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

  if ( isset ( $GLOBALS [$padField] ) )
    $GLOBALS [$padField]++;
  else
    $GLOBALS [$padField] = 1;

  return TRUE;

?>