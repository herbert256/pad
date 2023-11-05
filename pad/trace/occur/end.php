<?php

  if ( $padTraceResultOcc )
    padTrace ( 'occur', 'result', $padPad [$pad] );

  if ( $padTraceEndOcc )
    padTrace ( 'occur', 'end' );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( $padTraceOccur [$pad] [$padOccur[$pad]] );
  
  if ( $padTraceChilds )
    padTraceChilds ( $padTraceOccur [$pad] [$padOccur[$pad]], $padTraceOccurChilds[$pad] [$padOccur[$pad]], 'occur' );

  padTraceSet ( 'occur', 'end' );
   
?>