<?php

  if ( ! $padTraceResultLvl )
    return;

  if ( $padTraceDouble and $padTraceContent and $padBase [$pad] == $padResult [$pad] )
    return;

  padTrace ( 'level', 'result',  $padResult [$pad] ); 

?>