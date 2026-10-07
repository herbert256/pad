<?php

  // Everything that is not the level stack, tried in order: defined data stores,
  // sequences, globals, and finally a data store built on demand. Used by the all type
  // and by padAtSingle() for a bare name@ reference with no target.
  //
  // A path is first looked for from the top of each of them, and only then searched for in
  // depth: the deep search of a store came before the globals were tried at all, so a page's
  // $items lost {$items.0} to a {data} block holding [{"items":["X","Y"]}] - [X][Y][Cherry] -
  // and $user.name to a nested "user" of a store. A store the reference names ($type) is
  // still searched in depth first.

  for ( $padAtNoDeep = 1; $padAtNoDeep >= 0; $padAtNoDeep-- ) {

    $check = include PAD . 'at/types/_lib/check.php';
    if ( $check !== INF )
      return $check;

    $check = include PAD . 'at/types/sequences.php';
    if ( $check !== INF )
      return $check;

    $check = include PAD . 'at/types/globals.php';
    if ( $check !== INF )
      return $check;

  }

  $check = include PAD . 'at/types/_lib/new.php';
  if ( $check !== INF )
    return $check;

  return INF;

?>
