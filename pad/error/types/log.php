<?php


  include PAD . "error/error.php";


  /**
   * Error handler that logs errors and continues.
   *
   * This error action mode writes the error to the system error log
   * but allows execution to continue (non-fatal handling).
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return string Empty string to indicate continuation.
   */
  function padErrorGo ( $error, $file, $line ) {

    padLogError ( "$file:$line $error", 4 );

    return '';

  }


?>