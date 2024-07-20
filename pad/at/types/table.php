<?php

  global $pad;

  for ( $padIdx=$pad; $padIdx; $padIdx-- ) {

    if ( count ($names) == 1 and isset ( $padTable [$padIdx] [$name] ) )
      return $padTable [$padIdx] [$name];

    $check = padFindNames ( $padTable [$padIdx], $names );
    if ( $check !== INF )
       return $check;

  }

  return INF;

?>