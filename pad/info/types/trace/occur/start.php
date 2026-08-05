<?php

  // Traces the start of one occurrence - one pass of a data iteration - from events/occurStart.php.
  //
  // Besides the 'occur start' line it keeps the child counters: every enclosing level, and the
  // occurrence each of those is currently in, gains one child, which is the number
  // padInfoTraceChilds later stamps onto the directory names. With $padInfoTraceDataOcc the row
  // being iterated is traced as well and written as data-<n>.json beside it.

  if ( $padInfoTraceStartEndOcc )
   padInfoTrace ( 'occur', 'start' );

  for ( $padI = $pad; $padI >= 0; $padI-- ) {

    if ( ! isset ( $padInfoTraceLevelChilds [$padI] ) )
      $padInfoTraceLevelChilds [$padI] = 0;

    $padInfoTraceLevelChilds [$padI] ++;

    $padJ = $padOccur [$padI] ?? 0;

    if ( $padJ) {

       if ( ! isset ($padInfoTraceOccurChilds [$padI]         ) ) $padInfoTraceOccurChilds [$padI] [$padJ] = 0;
       if ( ! isset ($padInfoTraceOccurChilds [$padI] [$padJ] ) ) $padInfoTraceOccurChilds [$padI] [$padJ] = 0;

       $padInfoTraceOccurChilds [$padI] [$padJ] ++;

    }

  }

  if ( $padInfoTraceDataOcc ) {

    if ( ! $padInfoTraceDefault and ! count ( $padCurrent [$pad] ) )
      return;

   padInfoTrace ( 'occur', 'occ-data', $padCurrent [$pad] );

    $padJ = $padOccur [$pad];

    padInfoTraceWrite ( $pad, "data-$padJ.json", $padCurrent [$pad], 'file' );

  }

?>