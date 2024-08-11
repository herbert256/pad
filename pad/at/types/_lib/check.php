<?php

  global $padDataStore;

  if ( ! isset ( $padDataStore ) or ! is_array ( $padDataStore ) )
    return INF;
     
  if ( $type and isset ( $padDataStore [$type] ) ) {
    $check = padAtSearch ( $padDataStore [$type], $names ); 
    if ( $check !== INF ) 
      return $check;
  }

  foreach ( $padDataStore as $value) {
    $check = padAtSearch ( $value, $names ); 
    if ( $check !== INF ) 
      return $check;
  }

  return INF;

?>