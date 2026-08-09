<?php

  // Applies the closing tag's pipe. Run from level/end.php just before the level is torn
  // down, so $padPipeAfter [$pad] transforms $padResult [$pad] - every occurrence already
  // rendered and concatenated.

  if ( ! $padPipeAfter [$pad] )
    return;

  $padResult [$pad] = padEval ( $padPipeAfter [$pad], $padResult [$pad], TRUE );

?>