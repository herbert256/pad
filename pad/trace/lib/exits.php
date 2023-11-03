<?php

  global $padTraceSession, $padTraceActive;
  global $padTraceDumps, $padTraceBase;
 
  if ( $padTraceSession )
    foreach ( padSessionInfoEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  padTrace ( 'trace', 'end' );

  $padTraceActive = FALSE;

  padTraceCheckLocal ( $padTraceBase );

  if ( $padTraceDumps )
    padDumpToDir ( '', $padTraceBase . "/end" );

?>