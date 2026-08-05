<?php

  // The all type: search everywhere. First every open level (at/types/tags.php), then the
  // stores outside the level stack - data, sequences and globals - via
  // at/types/_lib/other.php. Returns the first hit, INF when nothing anywhere matched.

  $check = include PAD . 'at/types/tags.php';
  if ( $check !== INF )
    return $check;

  $check = include PAD . 'at/types/_lib/other.php';
  if ( $check !== INF )
    return $check;

  return INF;

?>