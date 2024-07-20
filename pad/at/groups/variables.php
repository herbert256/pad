<?php

  global $padSetLvl, $padSetOcc, $padParmParse;

  if ( count ( $names ) > 1 )
    return INF;

  $key = padAtKey ( $padParmParse [$padIdx], $name );

  if ( $key )
    if ( $padParmParse [$padIdx] [$key] == 'lvl' ) return $padSetLvl [$padIdx] [$key];
    else                                           return $GLOBALS [$key]; 

  if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl [$padIdx] [$name]; 
  if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [$name]; 

  return INF;

?>