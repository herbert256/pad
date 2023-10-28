<?php

  if ( $padTraceTree and $padTraceTypes ['dumps'] )
    padDumpToDir ( '', $padTraceLocation . "/END" );

  padTrace ( 'trace', 'end' );

  $padTraceActive = FALSE;

?>