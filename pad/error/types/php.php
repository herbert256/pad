<?php


  padErrorRestoreBoot ();

  ini_set ( 'display_errors', $padDisplayErrors );

  error_reporting ( $padErrorReporting );


  /**
   * Error handler that throws exceptions for PHP-native handling.
   *
   * This error action mode restores PHP's native error settings and
   * converts errors to exceptions, letting PHP handle display/logging.
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return string Never reached - always throws.
   *
   * @throws Exception Always throws with the error details.
   */
  function padErrorGo ( $error, $file, $line ) {

    throw new Exception ( "$file:$line $error" );

  }


?>