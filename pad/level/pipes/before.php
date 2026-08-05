<?php

  // Applies the opening tag's pipe. $padBase [$pad] - the level's content as the tag
  // handler left it - is run through $padPipeBefore [$pad] before any occurrence is
  // rendered, which is what makes {echo $x | upper} produce a transformed value.

  if ( ! $padPipeBefore [$pad] )
    return;

  $padBase [$pad] = padEval ( $padPipeBefore [$pad], $padBase [$pad] );

?>