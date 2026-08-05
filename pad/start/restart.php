<?php

  // Throws away everything rendered so far and runs a different page in its place, without
  // a browser round trip. padRestart() sets $padRestart, level/level.php notices it on its
  // next pass and lands here; exits/output/file.php uses the same route to carry on with the
  // next page after writing the output to a file.
  //
  // Any buffered output is dropped, $padRestart becomes the new page, and the variables
  // handed to padRestart() are promoted to globals so the new page can see them. Control
  // then goes back to start/pad/go.php, which re-runs the whole inits/level/exits cycle -
  // note this is a nested include, not a jump, so the abandoned run stays on the PHP stack.

  padEmptyBuffers ( $padIgnored );

  $padPage = $padRestart;

  if ( isset ( $padRestartVars ) ) {

    foreach ( $padRestartVars as $padK => $padV )
      $GLOBALS [$padK] = $padV;

    $padRestartVars = [];

  }

  include PAD . 'start/pad/go.php';

?>