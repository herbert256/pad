<?php

  if ( ! isset ( $padInfoTraceLevel [$pad] ) ) padInfoTraceSet ( $pad );
  if ( ! $padInfoTraceLevel [$pad]           ) padInfoTraceSet ( $pad );

  if ( $padInfoTraceStartEndLvl )
   padInfoTrace ( 'level', 'end', $padResult[$pad] );

  if ( $padInfoTraceStatus )
    padInfoTraceStatus ( );

  if ( $padInfoTraceLocalChk ) {
    padInfoTraceCheckLocal ( $padInfoTraceLevel [$pad] . '/0'     );
    padInfoTraceCheckLocal ( $padInfoTraceLevel [$pad] . '/inits' );
    padInfoTraceCheckLocal ( $padInfoTraceLevel [$pad]            );
    padInfoTraceCheckLocal ( $padInfoTraceLevel [$pad] . '/99999' );
    padInfoTraceCheckLocal ( $padInfoTraceLevel [$pad] . '/exits' );
  }

  if ( ! isset ( $padInfoTraceLevelChilds [$pad] ) )
    $padInfoTraceLevelChilds [$pad] = 0;

  if ( $padInfoTraceChilds )
    padInfoTraceChilds ( $padInfoTraceLevel [$pad], $padInfoTraceLevelChilds [$pad], 'level' );

  if ( $pad > 0 and ! $padInfoTraceKeepEmpty and $padInfoTraceLevel [$pad] and ! $padInfoTraceLevelChilds [$pad] )
    padInfoTraceDeleteDir ( DAT . $padInfoTraceDir . $padInfoTraceLevel [$pad] . '/' );

  $padInfoTraceLevel [$pad] = '';

  $padInfoTraceLevelChilds [$pad] = 0;
  $padInfoTraceOccurChilds [$pad] = [];

?>
