<?php

  // The data type: search the data stores. Already defined stores first
  // (at/types/_lib/check.php), and if the reference names a store that does not exist yet,
  // at/types/_lib/new.php tries to define it on the spot.

  $check = include PAD . 'at/types/_lib/check.php';
  if ( $check !== INF )
    return $check;

  $check = include PAD . 'at/types/_lib/new.php';
  if ( $check !== INF )
    return $check;

  return INF;

?>