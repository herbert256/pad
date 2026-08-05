<?php

  // Resolves PAD tags embedded in a closing tag's option text before those options are
  // parsed.
  //
  // Included from level/parms/parms.php when the options came from the closing tag. The
  // main loop works left to right, so a {...} sitting inside a closing tag has not been
  // rendered yet; padCode() runs the option text as a template snippet and, if that changed
  // it, level/between.php re-parses the tag from the rendered result.

  if ( ! str_contains( $padOpt [$pad] [0], '}' ) )
    return;

  $padClosePad = padCode ( $padOpt [$pad] [0] );

  if ( $padClosePad == $padOpt [$pad] [0] )
    return;

  $padBetween = $padTag [$pad] . ' ' . $padClosePad;

  include PAD . 'level/between.php';

?>