<?php
  
  for ( $i=$pad; $i >= 0; $i-- )

    if ( $name ) {

      if ( isset ( $padTable [$i] [$name] ) ) {
        $current = padAtSearch ( $padTable [$i] [$name], $names ); 
        if ( $current !== INF ) 
          return $current;
      }

    } else {

      foreach ( $padTable [$i] as $value) {
        $current = padAtSearch ( $value, $names ); 
        if ( $current !== INF ) 
          return $current;
      }

    }

  return INF;

?>