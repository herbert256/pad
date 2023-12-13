<?php

  for ( $i=$pad; $i; $i-- ) {

    $check = padAtSearch ( $padCurrent [$i], $names );
    if ( $check !== INF ) 
      return $check;

    foreach ( $padTable [$i] as $value ) {
      $check = padAtSearch ( $value, $names );
      if ( $check !== INF ) 
        return $check;
    }

  }

  $check = padAtSearch ( $padDataStore, $names );
  if ( $check !== INF ) 
    return $check;

  $check = padAtSearch ( $padSeqStore, $names );
  if ( $check !== INF ) 
    return $check;

  $check = padAtSearch ( $GLOBALS, $names );
  if ( $check !== INF ) 
    return $check;

  for ( $i=$pad; $i; $i-- ) {

    $check = padFindNames ( $padCurrent [$i], $names );
    if ( $check !== INF ) 
      return $check;

    $check = padFindNames ( $padData [$i], $names );
    if ( $check !== INF ) 
      return $check;

  }

  if ( isset ($padDataStore) ) {   
    $check = padFindNames ( $padDataStore, $names );
    if ( $check !== INF ) 
      return $check;
  }

  return padFindNames ( $GLOBALS, $names );

?>