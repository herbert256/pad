<?php

  for ( $i=$pad; $i >= 0; $i-- ) 
    
    if ( isset ( $padTable [$i] [$name] ) ) {
      $current = padDotSearch ( $padTable [$i] [$name], $names, $type ); 
      if ( $current !== INF ) 
        return $current;
    }

  return INF;

?>