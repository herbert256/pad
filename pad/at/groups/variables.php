<?php

   global $padSetLvl, $padSetOcc, $padParmParse;

   if ( count ( $names ) == 1 and ctype_digit ( $name ) ) {

    $key  = padAtKey ( $padParmParse [$padIdx], $name );

    if ( $key )
      if ( $padParmParse [$padIdx] [$key] == 'lvl' ) return $padSetLvl [$padIdx] [$key];
      else                                           return $GLOBALS [$key]; 

   }

   if ( count ( $names ) == 1 ) {
     if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl [$padIdx] [$name]; 
     if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [$name]; 
   }

  return INF;

?>