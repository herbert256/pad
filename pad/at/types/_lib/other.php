<?php

  // Everything that is not the level stack, tried in order: defined data stores,
  // sequences, globals, and finally a data store built on demand. Used by the all type
  // and by padAtSingle() for a bare name@ reference with no target.

  $check = include PAD . 'at/types/_lib/check.php';
  if ( $check !== INF )
    return $check;

  $check = include PAD . 'at/types/sequences.php';
  if ( $check !== INF )
    return $check;

  $check = include PAD . 'at/types/globals.php';
  if ( $check !== INF )
    return $check;

  $check = include PAD . 'at/types/_lib/new.php';
  if ( $check !== INF )
    return $check;

  return INF;

?>