<?php

  include pad . 'trace/items/result.php';   
  
  if ( $padTraceEndLvl )
    padTrace ( 'level', 'end' );

  if ( $padTraceStatus )
    padTraceStatus ( $padTraceLevel [$pad] );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( $padTraceLevel [$pad] );
  
  if ( $padTraceChilds )
    padTraceChilds ( $padTraceLevel [$pad], $padTraceLevelChilds[$pad], 'level' );

  padTraceSet ( 'level', 'end' );
  
?>