<?php

  for ( $i=$pad; $i >= 0; $i-- ) {

    if ( $name and $padName [$i] <> $name )
      continue;

    $padOptAt = $padOpt [$i];
    unset ( $padOptAt [0] );
    $current = padAtSearch ( $padOptAt, $names );
    if ( $current !== INF ) 
      return $current;

  }

  return INF;

?>