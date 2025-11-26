<?php

  include 'events/resultOcc.php';     

  if ( ! isset ( $padInfoTraceLevel [$pad] ) ) padInfoTraceSet ( $pad );
  if ( ! $padInfoTraceLevel [$pad]           ) padInfoTraceSet ( $pad );

  $padI = $padOccur [$pad] ?? 0;

  if ( ! isset ($padInfoTraceOccurChilds [$pad]         ) ) $padInfoTraceOccurChilds [$pad] [$padI] = 0;
  if ( ! isset ($padInfoTraceOccurChilds [$pad] [$padI] ) ) $padInfoTraceOccurChilds [$pad] [$padI] = 0;

  if ( $padInfoTraceStartEndOcc )
   padInfoTrace ( 'occur', 'end', $padOut [$pad] );

  if ( $padInfoTraceLocalChk )
    padInfoTraceCheckLocal ( $padInfoTraceLevel [$pad] . "/$padI" );
  
  if ( $padInfoTraceChilds )
    padInfoTraceChilds ( $padInfoTraceLevel [$pad] . "/$padI", $padInfoTraceOccurChilds [$pad] [$padI], 'occur' );

  $padInfoTraceOccurChilds [$pad] [$padI] = 0;
   
?>