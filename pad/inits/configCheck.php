<?php

  // Validates the closed configuration words before anything includes a file named after
  // them: the error action against error/types/, the output type against config/output/.
  //
  // This runs before inits/error.php has installed padErrorGo, so a fault is thrown at
  // the boot handlers instead - they stand from the first line of the request - after the
  // word is put back to a working default, so whatever reports has ground to stand on.
  // Included twice by inits/config.php, since the application's second pass may change
  // either word.

  if ( ! file_exists ( PAD . "error/types/$padErrorAction.php" ) ) {

    $padConfigBad   = $padErrorAction;
    $padErrorAction = 'boot';

    throw new \ErrorException ( "PAD: there is no error action named '$padConfigBad'" );

  }

  if ( ! file_exists ( PAD . "config/output/$padOutputType.php" ) ) {

    $padConfigBad   = $padOutputType;
    $padOutputType  = 'web';

    throw new \ErrorException ( "PAD: there is no output type named '$padConfigBad'" );

  }

?>