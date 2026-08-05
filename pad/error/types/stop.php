<?php

  // $padErrorAction 'stop': end the request on the first error with a plain 500, saying
  // nothing about what went wrong. Unlike 'exit' it leaves through padExit, so sessions are
  // closed, buffers emptied and headers sent as usual.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    padExit ( 500 );

  }

?>