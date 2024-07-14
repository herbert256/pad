<?php

  if ( strlen($field) > 1 and substr($field,0,1) == '-' and is_numeric(substr($field,1)) ) {
    $idx = $pad + $field;
    if ( $idx and isset ($padCurrent [$idx]) and is_array ($padCurrent [$idx]) )
      foreach ($padCurrent [$idx] as $value)
        if ( is_scalar($value) )
          return $value;
  }

  if ( is_numeric($field) ) 
    if ( array_key_exists ( $field, $padOpt [$pad] ) )
      return $padOpt [$pad] [$field];

  for ( $i=$pad; $i; $i-- ) {

    if ( array_key_exists ( $field, $padCurrent [$i] ) )
      return $padCurrent [$i] [$field];

    foreach ( $padTable [$i] as $value )
      if ( array_key_exists ( $field, $value ) )
        return $value [$field];

    }

  if ( array_key_exists ( $field, $GLOBALS ) ) 
    return $GLOBALS [$field];

  for ( $i=$pad; $i; $i-- ) {

    $padOptAt = $padOpt [$i];
    unset ( $padOptAt [0] );
    if ( array_key_exists ( $field, $padOptAt ) )
      return $padOptAt [$field];

    $check = padAtOneFind ( $padCurrent [$i], $field );
    if ( $check !== INF )
      return $check;

    $check = padAtOneFind ( $padData [$i], $field );
    if ( $check !== INF )
      return $check;

  }

  $check = padAtOneFind ( $GLOBALS, $field );
  if ( $check !== INF )
    return $check;

  return INF;   

?>