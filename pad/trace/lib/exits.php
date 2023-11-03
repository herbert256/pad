<?php

  if ( $padTraceSession )
    foreach ( padSessionInfoEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  padTrace ( 'trace', 'end' );

  $padTraceActive = FALSE;

 if ( $padTraceTace or $padTracelocal )
    padTraceCheckLocal ( $padTraceDirBase );

  if ( $padTraceDumps )
    padDumpToDir ( '', $padTraceBase . "/end" );

?>