<?php

  $check = include pad . 'at/any/tags.php';
  if ( $check !== INF )
     return $check;

  $check = include pad . 'var/types/data.php';
  if ( $check !== INF )
     return $check;

  $check = include pad . 'var/types/sequence.php';
  if ( $check !== INF )
     return $check;

  $check = include pad . 'var/types/saved.php';
  if ( $check !== INF )
     return $check;

  return include pad . 'var/types/globals.php';

?>