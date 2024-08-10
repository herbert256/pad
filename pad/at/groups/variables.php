<?php

  global $padSetLvl, $padSetOcc, $padParmParse;

  $temp = [];

  foreach ( $padParmParse [$padIdx] as $key => $val )
    if ( $val == 'lvl' ) $temp [$key] = $padSetLvl [$padIdx] [$key];
    else                 $temp [$key] = $GLOBALS [$key]; 

  $check = padAtSearch ( $temp, $names );
  if ( $check !== INF )
    return $check;

  $key = padAtKey ( $padParmParse [$padIdx], $name );

  if ( $key )
    if ( $padParmParse [$padIdx] [$key] == 'lvl' ) return $padSetLvl [$padIdx] [$key];
    else                                           return $GLOBALS [$key]; 

  if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl [$padIdx] [$name]; 
  if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [$name]; 

  return INF;

?>