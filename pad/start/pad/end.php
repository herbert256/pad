<?php

  // Puts back what start/pad/start.php saved, once a nested pass has finished.
  //
  // The pass flags and the pad-prefixed engine globals are always restored; an isolated pass
  // also gets its per-level data arrays and stores back. A sandbox or clean pass additionally
  // has everything it newly created removed, which is what separates clean (leave no trace)
  // from a plain reset (only wanted a blank slate going in), and the application variables
  // are restored last so they win over anything the pass left behind. $padStrCnt then drops
  // back to the enclosing pass.

  include PAD . 'start/end/end.php';
  include PAD . 'start/end/pad.php';

  if ( $padStrBox or $padStrCln or $padStrRes )  {
    include PAD . 'start/end/dat.php';
    include PAD . 'start/end/stores.php';
  }

  if ( $padStrBox or $padStrCln ) {
    include PAD . 'start/end/unsetApp.php';
    include PAD . 'start/end/unsetPad.php';
  }

  if ( $padStrBox or $padStrCln or $padStrRes )
    include PAD . 'start/end/app.php';

  $padStrCnt--;

?>