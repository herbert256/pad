<?php

  if ( $padTraceTree and $padTraceTypes ['dumps'] )
    padDumpToDir ( '', $padTraceLocation . "/end" );

  padTrace ( 'trace', 'end' );

  $padTraceActive = FALSE;

?>