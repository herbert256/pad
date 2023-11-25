<?php

  if ( ! $padTraceResultLvl )
    return;

  if ( $padTraceDouble and $padTraceContent and $padPadStart [$pad] == $padResult [$pad] )
    return;

  padTrace ( 'level', 'result',  $padResult [$pad] ); 

?>