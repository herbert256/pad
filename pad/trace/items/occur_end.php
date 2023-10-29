<?php

  if ( ! $padTraceTypes ['occur'] )
    return;

  if ( $padWalk [$pad] <> 'next' and padIsDefaultData ( $padData [$pad] ) )
    return;
  
  padTrace ( 'occur', 'end', $padPad [$pad] );

  if ( $padTraceTypes ['result'] and $padPad [$pad] and strlen ( $padPad [$pad] ) > 50 )
    padTraceFile ( 'result', 'pad', $padPad [$pad] ); 
   
?>