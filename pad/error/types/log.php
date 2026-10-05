<?php

  // $padErrorAction 'log': append "file:line message" to the PAD error log and carry on.
  // padErrorGo returns rather than exiting, so nothing reaches the visitor and the page
  // finishes rendering. It answers TRUE, as the ignore action does - error/error.php's
  // handlers answer PHP for themselves, and padError answers FALSE whatever it hears.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    padEventError ( $error, $file, $line );

    padLogError ( "$file:$line $error", 4 );

    return TRUE;

  }

?>