<?php

  // Re-rolls the sequence parameter when it was written as a random range, 'from..to'.
  //
  // Included as an expression by build/one.php once per candidate - so every term can draw
  // a fresh parameter - and returns the parameter to use. $pqRandomParm holds the original
  // range text, kept by build/vars.php. The range sequence is excluded because '..' is its
  // own notation there.

  if ( $pqRandomParm and $pqSeq != 'range' ) {
    $pqParm = $pqRandomParm;
    pqRandomParm ( $pqParm );
  }

  return $pqParm;

?>