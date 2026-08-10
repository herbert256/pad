<?php

  // $padErrorAction 'log': append "file:line message" to the PAD error log and carry on.
  // padErrorGo returns '' rather than exiting, so nothing reaches the visitor and the page
  // finishes rendering.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    padLogError ( "$file:$line $error", 4 );

    return TRUE;

  }

?>