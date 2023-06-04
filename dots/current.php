<?php

  for ( $i=$pad; $i >= 0; $i-- ) 

    if ( isset ( $padCurrent [$i] [$name] ) ) {
      $current = padDotSearch ( $padCurrent [$i] [$name], $names, $type ); 
      if ( $current !== INF ) 
        return $current;
    }

  return INF;

?>