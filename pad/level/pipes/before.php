<?php

  // Applies the opening tag's pipe. $padBase [$pad] - the level's content as the tag
  // handler left it - is run through $padPipeBefore [$pad] before any occurrence is
  // rendered, which is what makes {echo $x | upper} produce a transformed value.
  //
  // A base that is the tag's answer alone is protected here, once the pipe has had the
  // real value - level/go.php leaves it to this point under $padProtectValues.

  if ( $padPipeBefore [$pad] )
    $padBase [$pad] = padEval ( $padPipeBefore [$pad], $padBase [$pad], TRUE );

  if ( $padBaseValue [$pad] )
    $padBase [$pad] = padProtect ( $padBase [$pad] );

?>