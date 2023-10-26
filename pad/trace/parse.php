<?php

  if ( ! $padTraceTypes ['parse'] )
    return;

  if (  $padStart [$pad] > 50 ) {
    $padTraceStart = $padStart [$pad] - 50;
    $padTraceEnd   = 50;
  } else {
    $padTraceStart = 0;
    $padTraceEnd   = $padStart [$pad];
  }

  padTrace ( 'parse', 'start',  '{' . $padBetween . '}');
  padTrace ( 'parse', 'before', substr ( $padPad [$pad], $padTraceStart, $padTraceEnd ) ); 
  padTrace ( 'parse', 'after',  substr ( $padPad [$pad], $padEnd [$pad] + 1           ) ); 

?>