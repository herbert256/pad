<?php

  // Runs a nested PAD pass - a second trip through the engine from inside the current one -
  // and returns what it rendered.
  //
  // start/pad/start.php saves the engine state and, for a sandboxed or reset pass, clears
  // it; inits/level.php opens a fresh level, so the pass runs at $pad+1;
  // start/pad/$padStrBld.php fills that level's base, either from a source string
  // (code.php) or by building a page (page.php); start/pad/level.php then runs the level
  // loop until that level closes again, and start/pad/end.php puts the saved state back.
  // The nested level's output, $padOut[$pad+1], is the value returned to the include.

  include PAD . 'start/pad/start.php';
  include PAD . 'inits/level.php';
  include PAD . "start/pad/$padStrBld.php";
  include PAD . 'start/pad/level.php';
  include PAD . 'start/pad/end.php';

  return $padOut [$pad+1] ;

?>