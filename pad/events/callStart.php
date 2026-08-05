<?php

  // Fires from pad/call/_call.php and pad/call/_once.php immediately before the PHP file
  // named by $padCall is executed; stamps $padCallStart so events/callEnd.php can charge
  // the elapsed time to the called code rather than to the engine.
  //
  // Only absolute paths are timed, and only while $padInfo is on - the includes that fire
  // this event are guarded by it, so an untraced run costs nothing.

  if ( $padCall [0] == '/' )
    $padCallStart = hrtime ( TRUE );

 ?>