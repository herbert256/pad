<?php

  global $pad;

  for ( $padLoop=$pad; $padLoop; $padLoop-- ) {

    $padIdx = $padLoop + $cor;

    $check = include pad . 'at/any/tag.php';
    if ( $check !== INF )
      return $check;

  }

  return INF;

?>