<?php

  // Opens a new occurrence: bumps the row counter, gives the level a fresh working copy
  // of its template ($padOut = $padBase, its comments taken out and its ~ whitespace
  // control applied) and points
  // $padKey/$padCurrent at the row the data pointer currently sits on.
  //
  // Rows reached after the first walk pass are also collected in $padWalkData, which is
  // what the toData= option ends up storing.

  $padOccur [$pad]++;

  $padParm = $padOpt [$pad] [1] ?? '';

  $padOccurStart [$pad] [$padOccur[$pad]] = TRUE;

  $padOut     [$pad] = padTildeStrip ( padCommentStrip ( $padBase [$pad] ) );
  $padScan    [$pad] = 0;

  // A level no tag opened - the page itself when its PHP answered no list (build/page.php
  // wraps a list in a {padBuild} tag), or a {code} pass - never went through
  // level/start.php, so its own @start@ and @end@ reached the page as written: z99 printed
  // "@start@ bb @end@". It renders once, so its prelude, body and coda render once each:
  // padSectionsOnce takes its own markers out. Only for the passes start/pad/pad.php names
  // - a _data file the engine runs through padCode() keeps a marker in its text.

  if ( $padTag [$pad] == 'internal' and ( $padSectionsOnce [$pad] ?? FALSE ) and str_contains ( $padOut [$pad], '@' ) )
    $padOut [$pad] = padSectionsOnce ( $padOut [$pad] );
  $padKey     [$pad] = key($padData [$pad]);
  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  if ( $padWalk [$pad] != 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

?>
