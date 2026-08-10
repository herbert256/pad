<?php

  // Saves the engine state before a nested pass and clears whatever the pass wants to be
  // isolated from. $padStrCnt is the depth of nested passes, so every save is stacked and
  // passes can nest; start/pad/end.php unwinds one entry.
  //
  // The pass flags and the engine's own pad-prefixed globals are always saved, because they
  // must be put back whatever the pass does. On top of that, a sandbox, clean or reset pass
  // also saves the application variables, the per-level data arrays and the stores; sandbox
  // and reset then wipe those, so the pass starts from a blank slate; and all three finish by
  // seeding the scope with the variables {set} on the calling tag, which is how a caller
  // hands values into an otherwise isolated pass.

  $padStrCnt++;

  include PAD . 'start/start/start.php';
  include PAD . 'start/start/pad.php';

  // A sandboxed pass renders over hidden state, so a tag over a hidden store is not a
  // typo - the strict syntax check stands down for the pass, and the snapshot above puts
  // the outer setting back afterwards.

  if ( $padStrBox )
    $padCheckSyntax = FALSE;

  if ( $padStrBox or $padStrCln or $padStrRes ) {
    include PAD . 'start/start/app.php';
    include PAD . 'start/start/dat.php';
    include PAD . 'start/start/stores.php';
  }

  if ( $padStrBox or $padStrRes ) {
    include PAD . 'start/start/resetPad.php';
    include PAD . 'start/start/resetApp.php';
  }

  if ( $padStrBox or $padStrCln or $padStrRes )
    include PAD . 'start/start/level.php';

?>