<?php

  // Opens an @end@ construct: the part of the level's text after the marker is a coda that
  // renders once, after every occurrence has been walked.
  //
  // It is cut off into $padEndBase [$pad] and the part before it stays as the loop body;
  // level/end.php sees the pending $padEndBase when the data runs out and hands over to
  // start_end/end2.php.

  if ( $padInfo )
    include PAD . 'events/end.php';

  // Cut at the level's own marker, not at the first one - that may be a nested level's.

  $padSectionPos = padOpenClosePos ( $padBase [$pad], '@end@' );

  $padEndBase [$pad] = substr ( $padBase [$pad], $padSectionPos + 5 );
  $padBase    [$pad] = substr ( $padBase [$pad], 0, $padSectionPos );

?>
