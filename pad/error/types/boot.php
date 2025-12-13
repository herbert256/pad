<?php


  /**
   * Error handler that delegates to boot-time error handling.
   *
   * This error action mode uses the boot error handler, which displays
   * errors appropriately for local/remote environments and exits.
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return mixed Return value from padBootStop().
   */
  function padErrorGo ( $error, $file, $line ) {

    return padBootStop ( $error, $file, $line );

  }

    
?>