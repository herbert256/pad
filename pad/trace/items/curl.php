<?php

  if ( ! $GLOBALS ['padTraceTypes'] ['curl'] )
    return;

  padTrace ( 'curl', 'file', $url ); 

  padTraceFile ( 'curl', 'json',  $output );

?>