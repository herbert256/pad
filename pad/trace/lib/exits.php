<?php

  if ( $padTraceItems ['session'] )
    foreach ( padTrackFileRequestEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  padTrace ( 'trace', 'end' );

  $padTraceActive = FALSE;

 if ( $padTraceTace or $padTracelocal )
    padTraceCheckLocal ( $padTraceDirBase );

  if ( $padTraceItems ['dumps'] )
    padDumpToDir ( '', $padTraceDir . "/end" );

?>