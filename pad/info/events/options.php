<?php

  if ( $GLOBALS ['padInfo'] )
    if ( $padInfTraceOptions )
     padTrace ( 'option', $padOptionName, "type ==> $padOptions" );

  if ( $GLOBALS ['padInfo'] )
    include '/pad/info/events/options.php'; 

  padXapp ( 'options', $padOptionName );
  
?>