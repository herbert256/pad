<?php

  // Peels the opening pipe off a tag: {tag parms | expr}.
  //
  // $padBetweenOrg keeps the untouched text (level/no.php puts that back when the tag turns
  // out to be unknown), then $padBetween is cut at the first '|' that is not inside quotes
  // and the right-hand side is parked in $padPipeBeforeSet for level/setup.php. The
  // variable forms ($ ! # & ? @) are left alone - level/var.php splits its own pipe.

  $padBetweenOrg = $padBetween;

  if ( $padBetween and in_array ( $padBetween[0], ['$','!','#','&','?','@'] ) )
    return;

  $padPipeBeforeSet = $padPipeAfterSet = '';

  list ( $padSplitBefore, $padSplitAfter) = padPipeSplit ( $padBetween );

  if ( $padSplitAfter ) {
    $padBetweenOrg    = $padBetween;
    $padBetween       = $padSplitBefore;
    $padPipeBeforeSet = $padSplitAfter;
  }

?>