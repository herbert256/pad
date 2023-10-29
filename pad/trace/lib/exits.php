<?php

  padTrace ( 'trace', 'end' );

  $padTraceActive = FALSE;

  if ( $padTraceTypes ['dumps'] )
    padDumpToDir ( '', $padTraceDir . "/end" );

?>