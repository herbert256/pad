<?php

  list ( $padSplitBefore, $padSplitAfter ) = padPipeSplit (  $padBetweenCheck );

  if ( $padSplitAfter ) {
    $padBetweenCheck = $padSplitBefore;
    $padPipeAfterSet = $padSplitAfter;
  }

?>