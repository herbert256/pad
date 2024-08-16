<?php

  include '/pad/info/events/resultOcc.php';     

  if ( ! isset ( $padInfTraceLevel [$pad] ) ) padTraceSet ( $pad );
  if ( ! $padInfTraceLevel [$pad]           ) padTraceSet ( $pad );

  $padI = $padOccur [$pad] ?? 0;

  if ( ! isset ($padInfTraceOccurChilds [$pad]         ) ) $padInfTraceOccurChilds [$pad] [$padI] = 0;
  if ( ! isset ($padInfTraceOccurChilds [$pad] [$padI] ) ) $padInfTraceOccurChilds [$pad] [$padI] = 0;

  if ( $padInfTraceStartEndOcc )
   $GLOBALS ['padInfo']( 'occur', 'end' );

  if ( $padInfTraceLocalChk )
    padTraceCheckLocal ( $padInfTraceLevel [$pad] . "/$padI" );
  
  if ( $padInfTraceChilds )
    padTraceChilds ( $padInfTraceLevel [$pad] . "/$padI", $padInfTraceOccurChilds [$pad] [$padI], 'occur' );

  $padInfTraceOccurChilds [$pad] [$padI] = 0;
   
?>