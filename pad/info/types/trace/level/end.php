<?php

  include pad . 'info/events/result.php';   
  
  if ( ! isset ( $padTraceLevel [$pad] ) ) padTraceSet ( $pad );
  if ( ! $padTraceLevel [$pad]           ) padTraceSet ( $pad );

  if ( $padTraceStartEndLvl )
    padTrace ( 'level', 'end' );

  if ( $padTraceStatus )
    padTraceStatus ( );

  if ( $padTraceLocalChk ) {
    padTraceCheckLocal ( $padTraceLevel [$pad] . '/0'     );
    padTraceCheckLocal ( $padTraceLevel [$pad] . '/inits' );
    padTraceCheckLocal ( $padTraceLevel [$pad]            );
    padTraceCheckLocal ( $padTraceLevel [$pad] . '/99999' );
    padTraceCheckLocal ( $padTraceLevel [$pad] . '/exits' );
  }
  
  if ( ! isset ( $padTraceLevelChilds [$pad] ) ) 
    $padTraceLevelChilds [$pad] = 0;

  if ( $padTraceChilds ) 
    padTraceChilds ( $padTraceLevel [$pad], $padTraceLevelChilds [$pad], 'level' );

  if ( $pad > 0 and ! $padTraceKeepEmpty and $padTraceLevel [$pad] and ! $padTraceLevelChilds [$pad] )
    padTraceDeleteDir ( padData . $padTraceDir . $padTraceLevel [$pad] . '/' ); 

  $padTraceLevel [$pad] = '';

  $padTraceLevelChilds [$pad] = 0;
  $padTraceOccurChilds [$pad] = [];

?>