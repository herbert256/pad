<?php

  // Fires from pad/level/flags.php the moment a tag's result has been classified, and again
  // as a sub-event of the per-level trace dump (pad/info/types/trace/level/info.php).
  //
  // Traces the four booleans that decide what the level does - $padHit, $padElse, $padNull,
  // $padArray - plus the occurrence count, when $padInfoTraceFlags is set.

  global $padInfoTrace;

  if ( $padInfoTrace and $padInfoTraceFlags )
    padInfoTrace ( 'level', 'flags',
      ' hit='     . $padHit   [$pad] .
      ' else='    . $padElse  [$pad] .
      ' null='    . $padNull  [$pad] .
      ' array='   . $padArray [$pad] .
      ' count='   . count ( $padData [$pad] )
    );

?>