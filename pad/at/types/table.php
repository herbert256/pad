<?php
  
  if ( $kind ) {

    if ( isset ( $padTable [$padIdx] [$kind] ) )
      return  padAtSearch ( $padTable [$padIdx] [$kind], $names ); 

  } else

    foreach ( $padTable [$padIdx] as $value) {
      $current = padAtSearch ( $value, $names ); 
      if ( $current !== INF ) 
        return $current;
    }

  return INF;

?>