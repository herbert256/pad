<?php

  // Installs the error handling the rest of the request will use, replacing the minimal boot
  // handlers set up in error/boot.php.
  //
  // $padErrorAction picks the strategy and this file loads the matching handler from
  // error/types/ - 'pad' to render the error into the page, plus boot, php, stop, exit,
  // ignore, log and dump. include_once, so a restart does not install it twice.
  //
  // Local curl requests (padClaudeCheck) are forced back to the boot handler, which answers
  // with a 500 and a JSON dump of the engine state instead of an HTML page - much easier to
  // read from the command line.

  if ( padClaudeCheck ( ) ) {
    $padErrorAction = 'boot';
    return include_once PAD . "error/types/boot.php";
  }

  include_once PAD . "error/types/$padErrorAction.php";

?>