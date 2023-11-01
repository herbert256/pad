<?php

  if ( ! $padTraceItems ['false'] )
    return;

  padTrace ( 'level', 'false',  $padFalse [$pad] ); 

  if ( $padFalse [$pad] and strlen ( $padFalse [$pad] ) > 50 )
    padTraceFile ( 'false', 'pad', $padFalse [$pad] );

?>