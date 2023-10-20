<?php
  
  if ( $name ) {

    if ( isset ( $padTable [$i] [$name] ) )
      return  padAtSearch ( $padTable [$i] [$name], $names ); 

  } else

    foreach ( $padTable [$i] as $value) {
      $current = padAtSearch ( $value, $names ); 
      if ( $current !== INF ) 
        return $current;
    }

  return INF;

?>