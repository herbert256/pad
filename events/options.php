<?php

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS['padInfoTraceOptions'] )
    padInfoTrace ( 'option', $padOptionName, "type ==> $padOptions" );

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) 
    padInfoXapp ( 'options', $padOptionName );
  
?>