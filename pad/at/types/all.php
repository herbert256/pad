<?php

  $check = include 'at/types/tags.php';
  if ( $check !== INF )
    return $check;

  $check = include 'at/types/_lib/other.php';
  if ( $check !== INF )
    return $check;

  return INF;
  
?>