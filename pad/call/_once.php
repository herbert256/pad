<?php

  // As call/_call.php, but includes $padCall with include_once (via call/_tryOnce) so a file
  // already pulled in this request is not run a second time - used for the _lib/ function
  // files. On a repeat include PHP returns TRUE rather than the file's own return value.

  global $padInfo;

  include PAD . 'call/_init.php';

  if ( file_exists ( $padCall ) ) {

    if ( $padInfo )
      include PAD . 'events/callStart.php';

    $padCallPHP = include PAD . 'try/call/_tryOnce.php';

    if ( $padInfo )
      include PAD . 'events/callEnd.php';

  }

  include PAD . 'call/_exit.php';

 ?>