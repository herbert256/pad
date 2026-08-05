<?php

  // $padErrorAction 'php': hand error handling back to PHP. Takes the boot net down and puts
  // display_errors and error_reporting back to the values error/boot.php saved in
  // $padDisplayErrors and $padErrorReporting, so PHP reports in its own way.
  //
  // No handlers of PAD's own are installed - error/error.php is deliberately not included -
  // and padErrorGo, the hook padError() calls, simply throws, leaving the outcome to PHP.

  padErrorRestoreBoot ();

  ini_set ( 'display_errors', $padDisplayErrors );

  error_reporting ( $padErrorReporting );

  function padErrorGo ( $error, $file, $line ) {

    throw new Exception ( "$file:$line $error" );

  }

?>