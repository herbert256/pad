<?php


  include PAD . "error/error.php";


  /**
   * Error handler that stops execution with HTTP 500 status.
   *
   * This error action mode terminates execution immediately using padExit()
   * with a 500 status code. Similar to 'exit' but uses PAD's exit system.
   *
   * @param string $error The error message (unused).
   * @param string $file  The file where the error occurred (unused).
   * @param int    $line  The line number where the error occurred (unused).
   *
   * @return void Never returns - script exits.
   */
  function padErrorGo ( $error, $file, $line ) {

    padExit ( 500 );

  }


?>