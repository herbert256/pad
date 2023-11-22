<?php

  if ( ! $GLOBALS ['padTraceExists'] )
    return;

  if ( strpos ( '/trace/', $file ) )
    return;

  if ( $return )
    padTrace ( 'exists', 'true',  $file );
  else
    padTrace ( 'exists', 'false', $file );
   
?>