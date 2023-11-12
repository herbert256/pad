<?php
 
  if ( $padTraceSession )
    foreach ( padSessionInfoEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  padTrace ( 'trace', 'end' );

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  if ($padTraceLocalChk )
    padTraceCheckLocal ( $padTraceBase );

  if ( $padTraceDumps ) 
    padTraceDump ( 'dump-end' );
  
  $padTraceLevel [$pad] = '';

  $padTraceActive = FALSE;

?>