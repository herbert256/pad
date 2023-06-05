<?php

  $current = padDotSearch ( $padCurrent [$i], $names, $type ); 
  if ( $current !== INF ) 
    return $current;

  $current = padDotSearch ( $padTable [$i], $names, $type ); 
  if ( $current !== INF ) 
    return $current;

  $current = padDotSearch ( $padPrm [$i], $names, $type ); 
  if ( $current !== INF ) 
    return $current;

  $padOptDot = $padOpt [$i];
  unset ( $padOptDot [0] );

  $current = padDotSearch ( $padOptDot, $names, $type ); 
  if ( $current !== INF ) 
    return $current;

  $current = padDotSearch ( $padSetLvl [$i], $names, $type ); 
  if ( $current !== INF ) 
    return $current;      

  $current = padDotSearch ( $padData [$i], $names, $type ); 
  if ( $current !== INF ) 
    return $current;

  return INF;

?>