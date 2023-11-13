<?php

  if ( ! $padTraceResultLvl )
    return;

  if ( $padTraceNoDouble and $padTraceContent and $padPadStart [$pad] == $padResult [$pad] )
    return;

  padTrace ( 'level', 'result',  $padResult [$pad] ); 

?>