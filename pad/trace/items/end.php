<?php

  padTrace ( 'level', 'end', $padResult[$pad] );

  if ( $padTraceTypes ['local'] )
    padTraceCheckLocal ();

  if ( $pad ) 
    padTraceStatus ();
  
?>