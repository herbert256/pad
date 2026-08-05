<?php

  // Peels the closing pipe off a closing tag: {/tag | expr}. The text of the closing tag is
  // cut at the first unquoted '|' and the right-hand side is parked in $padPipeAfterSet,
  // which level/setup.php stores and level/pipes/after.php applies to the finished output.

  list ( $padSplitBefore, $padSplitAfter ) = padPipeSplit (  $padBetweenCheck );

  if ( $padSplitAfter ) {
    $padBetweenCheck = $padSplitBefore;
    $padPipeAfterSet = $padSplitAfter;
  }

?>