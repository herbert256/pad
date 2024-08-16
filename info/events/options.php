<?php

  if ( $GLOBALS ['padInfoTrace'] )
    if ( $padInfoTraceOptions )
     if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'option', $padOptionName, "type ==> $padOptions" );

  if ( $GLOBALS ['padInfoTrace'] )
    include '/pad/info/events/options.php'; 

  if ( $GLOBALS ['padInfoXapp'] ) padInfoXapp ( 'options', $padOptionName );
  
?>