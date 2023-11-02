<?php

  if ( ! $GLOBALS ['padTraceCurl'] )
    return;

  padTrace ( 'curl', 'file', $url ); 

  padTraceFile ( 'curl', 'json',  $output );

?>