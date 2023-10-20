<?php

  for ( $i=$pad; $i; $i-- ) 

    if ( isset ( $padTable [$i] [$name] ) ) {
      $current = include pad . 'var/table.php';
      if ( $current !== INF ) 
        return $current;
    }

  for ( $i=$pad; $i; $i-- ) {

    $check = padFindName ( $padCurrent [$i], $name, $names );
    if ( $check !== INF ) 
      return $check;

    $check = padFindName ( $padData [$i], $name, $names );
    if ( $check !== INF ) 
      return $check;

  } 

  if ( isset ( $padDataStore [$name] ) ) {
    $current = padFindNames ( $padDataStore [$name], $names );
    if ( $current !== INF ) 
      return $current;
  }

  if ( isset ( $padSeqStore [$name] ) ) {
    $current = padAtSearch ( $padSeqStore [$name], $names );
    if ( $current !== INF ) 
      return $current;
  }

  $current = padFindName ( $GLOBALS, $name, $names );
  if ( $current !== INF ) 
    return $current;

  if ( padDataFileName ( $name ) ) {
    $padDataStore [$name] = padData ( padDataFileData ( padDataFileName($name) ) );
    $current = padFindNames ( $padDataStore [$name], $names );
    if ( $current !== INF ) 
      return $current;
  }

  return INF;

?>