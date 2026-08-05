<?php

  // Handles the end option by delegating to handling/types/start.php, which resolves
  // start, end and rows together.
  //
  // Does nothing when the tag also carries a start option, since that option triggers the
  // very same handler and the window would otherwise be applied twice.

  if ( ! isset ( $padPrm [$pad] ['start'] ) )
    include PAD . 'handling/types/start.php';

?>