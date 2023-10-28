<?php

  if ( ! $GLOBALS ['padTraceTypes'] ['curl'] )
    return;

  if ( $GLOBALS ['padTraceTree'] ) {

    padTrace ( 'curl', 'file', $url ); 

    $file = "curl/" . $GLOBALS ['padTrace'];

    padTraceFile ( $file, 'json',  $output );

  }

  else {

    padTrace ( 'curl', 'start', $url ); 
    padTrace ( 'curl', 'end',   $output ['type'] ); 

  }

?>