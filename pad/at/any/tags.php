<?php

  global $pad;

  for ( $padIdx=$pad; $padIdx; $padIdx-- ) {

    $check = include pad . 'at/any/tag.php';
    if ( $check !== INF )
      return $check;

  }

  return INF;

?>