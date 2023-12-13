<?php

  if ( $padTraceFlags )
    padTrace ( 'level', 'flags', 
      ' hit='     . $padHit   [$pad] . 
      ' else='    . $padElse  [$pad] . 
      ' null='    . $padNull  [$pad] . 
      ' array='   . $padArray [$pad] . 
      ' default=' . $padDefault  [$pad] . 
      ' count='   . count ( $padData [$pad] )
    );

?>