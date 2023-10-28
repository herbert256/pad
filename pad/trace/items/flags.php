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

?>