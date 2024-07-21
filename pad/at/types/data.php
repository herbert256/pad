<?php

  if ( $type and isset ( $padDataStore [$type] ) ) {
    $current = padAtSearch ( $padDataStore [$type], $names ); 
    if ( $current !== INF ) 
      return $current;
  }

  foreach ( $padDataStore as $value) {
    $current = padAtSearch ( $value, $names ); 
    if ( $current !== INF ) 
      return $current;
  }

  if ( $type and ! isset ( $padDataStore [$type] ) ) {
    $padDataStore [$type] = padData ($type);
    $current = padAtSearch ( $padDataStore [$type], $names ); 
    if ( $current !== INF ) 
      return $current;
  }

  return INF;

?>