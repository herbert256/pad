<?php

  if ( ! $padTraceResultLvl )
    return;

  if ( $padTraceNoDouble and $padTraceContent and $padTraceStartContent [$pad] == $padResult [$pad] )
    return;

  padTrace ( 'level', 'result',  $padResult [$pad] ); 

?>