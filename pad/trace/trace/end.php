<?php
 
  if ( $padTraceSession )
    foreach ( padSessionInfoEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  if ( $padTraceOpenClose )
    padTrace ( 'trace', 'end' );

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  if ($padTraceLocalChk )
    padTraceCheckLocal ( $padTraceBase );

  $padTraceX1 = $padTraceLevel       [$pad] ?? '';
  $padTraceX2 = $padTraceLevelChilds [$pad] ?? 0;

  if ( $padTraceChilds )
    padTraceChilds ( $padTraceX1, $padTraceX2, 'level' );

  if ( $padTraceDumps ) 
    padTraceDump ( 'end' );

  padTraceSet ( 'trace', 'end' );

  if ( $padTraceXml and $padTraceXmlTidy )
    include pad . 'trace/trace/tidy.php';
  
  $padTraceActive = FALSE;

?>