<?php


  include PAD . "error/error.php";


  /**
   * Error handler that ignores all errors and continues.
   *
   * This error action mode suppresses all errors silently,
   * allowing execution to continue uninterrupted.
   *
   * @param string $error The error message (ignored).
   * @param string $file  The file where the error occurred (ignored).
   * @param int    $line  The line number where the error occurred (ignored).
   *
   * @return bool Always returns TRUE.
   */
  function padErrorGo ( $error, $file, $line ) {

    return TRUE;

  }


?>