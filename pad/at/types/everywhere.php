<?php

  global $padDataStore;

  $current = include pad . 'at/any/tags.php';
  if ( $current !== INF ) 
    return $current;

  if ( $type and isset ( $padDataStore [$type] ) ) {
    $current = padAtSearch ( $padDataStore [$type], $names ); 
    if ( $current !== INF ) 
      return $current;
  }

  if ( is_array($padDataStore))
  foreach ( $padDataStore as $value) {
    $current = padAtSearch ( $value, $names ); 
    if ( $current !== INF ) 
      return $current;
  }

  $current = include pad . 'at/types/sequences.php';
  if ( $current !== INF ) 
    return $current;

  $current = include pad . 'at/types/globals.php';
  if ( $current !== INF ) 
    return $current;
  
  return INF;
  
?>