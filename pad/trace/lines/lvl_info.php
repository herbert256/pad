<?php

  if ( $padTraceTypes ['flags'] )
    padTrace ( 'level', 'flags', 
      ' hit='   . $padHit   [$pad] . 
      ' else='  . $padElse  [$pad] . 
      ' null='  . $padNull  [$pad] . 
      ' array=' . $padArray [$pad] . 
      ' text='  . $padText  [$pad] . 
      ' count=' . count ( $padData [$pad] )
    );

  if ( $padTraceTypes ['true'] )
    padTrace ( 'level', 'true',  $padTrue  [$pad] ); 

  if ( $padTraceTypes ['false'] )
    padTrace ( 'level', 'false', $padFalse [$pad] ); 

?>