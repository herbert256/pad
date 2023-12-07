<?php

  include pad . 'info/events/resultOcc.php';     

  if ( ! isset ( $padTraceLevel [$pad] ) ) padTraceSet ( $pad );
  if ( ! $padTraceLevel [$pad]           ) padTraceSet ( $pad );

  $padI = $padOccur [$pad] ?? 0;

  if ( ! isset ($padTraceOccurChilds [$pad]         ) ) $padTraceOccurChilds [$pad] [$padI] = 0;
  if ( ! isset ($padTraceOccurChilds [$pad] [$padI] ) ) $padTraceOccurChilds [$pad] [$padI] = 0;

  if ( $padTraceStartEndOcc )
    padTrace ( 'occur', 'end' );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( $padTraceLevel [$pad] . "/$padI" );
  
  if ( $padTraceChilds )
    padTraceChilds ( $padTraceLevel [$pad] . "/$padI", $padTraceOccurChilds [$pad] [$padI], 'occur' );

  $padTraceOccurChilds [$pad] [$padI] = 0;
   
?>