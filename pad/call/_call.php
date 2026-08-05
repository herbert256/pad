<?php

  // Core of the engine's include-as-call mechanism: runs the PHP file named by $padCall and
  // captures both what it echoes and what it returns.
  //
  // The include goes through try/try.php (as call/_try) so a thrown exception becomes a PAD
  // error instead of killing the request; a missing file is not an error, the call simply
  // yields nothing. Leaves $padCallOB (echoed output) and $padCallPHP (return value) for the
  // wrapper that included this file - any / noOne / once / ob / obNoOne.

  global $padInfo;

  include PAD . 'call/_init.php';

  if ( file_exists ( $padCall ) ) {

    if ( $padInfo )
      include PAD . 'events/callStart.php';

    $padTry = 'call/_try';
    $padCallPHP = include PAD . 'try/try.php';

    if ( $padInfo )
      include PAD . 'events/callEnd.php';

  }

  include PAD . 'call/_exit.php';

 ?>