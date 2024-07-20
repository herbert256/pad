<?php

  global $pad;

  for ( $padIdx=$pad; $padIdx; $padIdx-- ) {

    $padIdx = $padIdx + $cor;

    $check = include pad . 'at/any/tag.php';
    if ( $check !== INF )
      return $check;

  }

  return INF;

?>