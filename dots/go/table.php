<?php

  if ( isset ( $padTable [$i] [$name] ) ) {
    $current = padDotSearch ( $padTable [$i] [$name], $names, $type ); 
    if ( $current !== INF ) 
      return $current;
  }

  if ( ! $first )
    foreach ($padTable [$i] as $key => $value ) 
      if ( isset ( $value [$name] ) )
        return padDotReturn ( $value [$name], $type );

  return INF;

?>