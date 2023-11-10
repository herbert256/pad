<?php

  include pad . 'trace/items/result.php';   
  
  if ( ! isset ( $padTraceLevel [$pad] ) ) padTraceSet ( $pad );
  if ( ! $padTraceLevel [$pad]           ) padTraceSet ( $pad );

  padTrace ( 'level', 'end' );

  if ( $padTraceStatus )
    padTraceStatus ( );

  if ( $padTraceLocalChk ) {
    padTraceCheckLocal ( $padTraceLevel [$pad] . '/0'     );
    padTraceCheckLocal ( $padTraceLevel [$pad]            );
    padTraceCheckLocal ( $padTraceLevel [$pad] . '/999' );
  }
  
  if ( ! isset ( $padTraceLevelChilds [$pad] ) ) 
    $padTraceLevelChilds [$pad] = 0;

  if ( $padTraceChilds ) 
    padTraceChilds ( $padTraceLevel [$pad], $padTraceLevelChilds [$pad], 'level' );

  $padTraceLevel [$pad] = '';

  $padTraceLevelChilds [$pad] = 0;
  $padTraceOccurChilds [$pad] = [];

?>