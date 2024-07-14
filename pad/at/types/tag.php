<?php

  $current = padAtSearch ( $padCurrent [$i], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearch ( $padTable [$i], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearch ( $padSetLvl [$i], $names );
  if ( $current !== INF ) 
    return $current;

  $padOptAt = $padOpt [$i];
  unset ( $padOptAt [0] );

  $current = padAtSearch ( $padOptAt , $names );
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearch ( $padPrm [$i], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padFindNames ( $padCurrent [$i], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padFindNames ( $padData [$i], $names );
  if ( $current !== INF ) 
    return $current;

  return INF;

?>