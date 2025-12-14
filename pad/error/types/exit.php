<?php


  include PAD . "error/error.php";


  /**
   * Error handler that silently exits the application.
   *
   * This error action mode immediately terminates execution without
   * displaying any error information. Used for silent failure.
   *
   * @param string $error The error message (unused).
   * @param string $file  The file where the error occurred (unused).
   * @param int    $line  The line number where the error occurred (unused).
   *
   * @return void Never returns - script exits.
   */
  function padErrorGo ( $error, $file, $line ) {

    include PAD . 'exits/exit.php';

  }


?>