<?php

  // $padErrorAction 'boot': never leave the boot net. Unlike the other actions this one does
  // not include error/error.php, so the handlers from error/boot.php stay installed and
  // padErrorGo simply hands anything raised by padError() to padBootStop - abort the request
  // with a plain 500, or the JSON payload of error/claude.php for a local tool request.
  //
  // inits/error.php forces this action whenever padClaudeCheck() is true.

  function padErrorGo ( $error, $file, $line ) {

    return padBootStop ( $error, $file, $line );

  }

?>