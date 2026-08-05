<?php

  // Repeats the single-level lookup of at/any/tag.php at every open level, from the
  // current level $pad down to the root, and returns the first hit ($cor shifts each
  // level index first). Reached through the tags type, i.e. a names@tags reference.

  global $pad;

  for ( $i=$pad; $i>-1; $i-- ) {

    $padIdx = $i + $cor;

    $check = include PAD . 'at/any/tag.php';
    if ( $check !== INF )
      return $check;

  }

  return INF;

?>