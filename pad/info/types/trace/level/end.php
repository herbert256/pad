<?php

  // Traces the closing of a level, from events/levelEnd.php, and tidies its directory.
  //
  // Writes the 'level end' line with the level's result, adds the status marker file
  // ($padInfoTraceStatus), deletes trace files of the level and its inits/exits occurrences that
  // turned out to duplicate one another, renames the directory to carry its child count, and
  // removes it entirely when the level produced no children and $padInfoTraceKeepEmpty is off.
  //
  // Finally clears the level's path and counters so the slot can serve the next tag at this depth.

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
    padInfoTraceDeleteDir ( DATA . $padInfoTraceDir . $padInfoTraceLevel [$pad] . '/' );

  $padInfoTraceLevel [$pad] = '';

  $padInfoTraceLevelChilds [$pad] = 0;
  $padInfoTraceOccurChilds [$pad] = [];

?>