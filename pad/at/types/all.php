<?php

  $check = include pad . 'at/types/tags.php';
  if ( $check !== INF )
    return $check;

  $check = include pad . 'at/types/_lib/other.php';
  if ( $check !== INF )
    return $check;

  return INF;
  
?>