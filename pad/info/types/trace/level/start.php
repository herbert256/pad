<?php

  // Traces the opening of a level: one 'level start' line carrying the raw tag text, rebuilt
  // from $padBetween as the parser found it between the braces.
  //
  // Reached from events/setup.php when a tag is about to be processed, and from
  // events/build.php for the levels the page assembly creates.

  if ( $padInfoTraceStartEndLvl )
     padInfoTrace ( 'level', 'start', '{' . $padBetween . '}');

?>