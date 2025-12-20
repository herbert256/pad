<?php

  global $padInfoTrace;

  if ( $padInfoTrace and $padInfoTraceFlags )
    padInfoTrace ( 'level', 'flags',
      ' hit='     . $padHit   [$pad] .
      ' else='    . $padElse  [$pad] .
      ' null='    . $padNull  [$pad] .
      ' array='   . $padArray [$pad] .
      ' count='   . count ( $padData [$pad] )
    );

?>
