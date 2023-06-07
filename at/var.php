<?php

  for ( $i=$pad; $i >= 0; $i-- ) {

    if ( $name and $padName [$i] <> $name )
      continue;

    $current = padAtSearch ( $padSetLvl [$i], $names );
    if ( $current !== INF ) 
      return $current;

  }

  return INF;

?>