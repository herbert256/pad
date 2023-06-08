<?php

  $keep = $names;

  $name = array_shift ($names);

  for ( $i=$pad; $i >=0; $i-- ) {

    if ( isset ( $padTable [$i] [$name] ) ) {
      $current = padAtSearch ( $padTable [$i] [$name], $names );
      if ( $current !== INF ) 
        return $current;
    }

    if ( $padName [$i] == $name ) {
      $current = include pad . 'var/at/tag.php';
      if ( $current !== INF ) 
        return $current;
    }

  }

  if ( isset ( $padSeqStore [$name] ) ) {
    $current = padAtSearch ( $padSeqStore [$name], $names );
    if ( $current !== INF ) 
      return $current;
  }

  if ( isset ( $padDataStore [$name] ) ) {
    $current = padAtSearch ( $padDataStore [$name], $names );
    if ( $current !== INF ) 
      return $current;
  }

  if ( isset ( $GLOBALS [$name] ) ) {
    $current = padAtSearch ( $GLOBALS [$name], $names );
    if ( $current !== INF ) 
      return $current;
  }

  $names = $keep;
  
  for ( $i=$pad; $i >=0; $i-- ) {

    $check = padAtNamesFind  ( $padCurrent [$i], $names );
    if ( $check !== INF )
      return $check;

    $check = padAtNamesFind  ( $padData [$i], $names );
    if ( $check !== INF )
      return $check;

  }

  $check = padAtNamesFind ( $GLOBALS, $names );
  if ( $check !== INF )
    return $check;
  return INF;   

?>