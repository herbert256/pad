<?php

   global $padSetLvl, $padSetOcc, $padParmParse;

  $debug [] = ['variables-1', $names, $padIdx, $name];    

   if ( count ( $names ) == 1 and ctype_digit ( $name ) ) {

    $keys = array_keys ( $padParmParse [$padIdx] );
    $key  = $keys [ $name - 1 ] ?? '';

    global $debug;
    $debug [] = ['variables-2', $key];    

    if ( $key )
      if ( $padParmParse [$padIdx] [$key] == 'lvl' ) return $padSetLvl [$padIdx] [$key];
      else                                           return $GLOBALS [$key]; 

   }

   if ( count ( $names ) == 1 ) {
    global $debug;
    $debug [] = ['variables-3', $padIdx, $name];    
     if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl [$padIdx] [$name]; 
     if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [$name]; 
   }

    global $debug;
    $debug [] = ['variables-4', 'xx'];    

  return INF;

?>