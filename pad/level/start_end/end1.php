<?php

  // Opens an @end@ construct: the part of the level's text after the marker is a coda that
  // renders once, after every occurrence has been walked.
  //
  // It is cut off into $padEndBase [$pad] and the part before it stays as the loop body;
  // level/end.php sees the pending $padEndBase when the data runs out and hands over to
  // start_end/end2.php.

  if ( $padInfo )
    include PAD . 'events/end.php';

  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );

?>