<?php

   global $padSetLvl, $padSetOcc;

   if ( count ( $names ) == 1 ) {
     $name = $names [0];
     if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl [$padIdx] [$name]; 
     if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [ $name ]; 
   }

  return INF;

?>