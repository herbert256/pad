<?php

  if ( ! $GLOBALS ['padTraceItems'] ['curl'] )
    return;

  padTrace ( 'curl', 'file', $url ); 

  padTraceFile ( 'curl', 'json',  $output );

?>