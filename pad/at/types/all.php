<?php

  $check = include PAD . 'at/types/tags.php';
  if ( $check !== INF )
    return $check;

  $check = include PAD . 'at/types/_lib/other.php';
  if ( $check !== INF )
    return $check;

  return INF;

?>
