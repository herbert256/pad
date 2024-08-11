<?php

  global $pad;

  for ( $i=$pad; $i>-1; $i-- ) {

    $padIdx = $i + $cor;

    $check = include pad . 'at/any/tag.php';
    if ( $check !== INF )
      return $check;

  }

  return INF;

?>