<?php


  include PAD . "error/error.php";


  /**
   * Error handler that dumps error to a file and continues.
   *
   * This error action mode writes the error to a dump file in the DATA
   * directory but allows execution to continue (non-fatal handling).
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return string Empty string to indicate continuation.
   */
  function padErrorGo ( $error, $file, $line ) {

    padDumpToDir ( "$file:$line $error" );

    unset ( $GLOBALS ['padDumpToDirDone'] );

    return '';

  }


?>