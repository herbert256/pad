<?php

  if ( ! $padTraceTypes ['occur'] )
    return;
  
  padTrace ( 'occur', 'end', $padPad [$pad] );

  if ( $padTraceTypes ['result'] and $padPad [$pad] and strlen ( $padPad [$pad] ) > 50 )
    padTraceFile ( 'occ-result', 'pad', $padPad [$pad] ); 
   
?>