<?php

  $check = include PAD . 'at/types/_lib/check.php';
  if ( $check !== INF )
    return $check;

  $check = include PAD . 'at/types/_lib/new.php';
  if ( $check !== INF )
    return $check;

  return INF;

?>
