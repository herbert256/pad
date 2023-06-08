<?php

  for ( $i=$pad; $i >= 0; $i-- ) {

    if ( $name and $padName [$i] <> $name )
      continue;

    if ( count ($names) == 1 ) {

      $check = reset ($names);

      if ( array_key_exists ( $check, $padCurrent [$i] ) )
        return $padCurrent [$i] [$check];

      foreach ( $padTable [$i] as $value )
        if ( array_key_exists ( $check, $value ) )
          return $value [$check];

      $padOptAt = $padOpt [$i];
      unset ( $padOptAt [0] );
      if ( array_key_exists ( $check, $padOptAt ) )
        return $padOptAt [$check];

      if ( array_key_exists ( $check, $padPrm [$i] ) )
        return $padPrm [$i] [$check];

$GLOBALS['x']=$names;
x();
      if ( array_key_exists ( $check, $padSetLvl [$i] ) )
        return $padSetLvl [$i] [$check];

    } else {

      $current = padAtSearch ( $padCurrent [$i], $names );
      if ( $current !== INF ) 
        return $current;

      $current = padAtSearch ( $padTable [$i], $names );
      if ( $current !== INF ) 
        return $current;

      $current = padAtNamesFind ( $padCurrent [$i], $names );
      if ( $current !== INF ) 
        return $current;

      $current = padAtNamesFind ( $padData [$i], $names );
      if ( $current !== INF ) 
        return $current;

    }
 
  }

  return INF;

?>