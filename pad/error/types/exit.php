<?php

  // $padErrorAction 'exit': drop the request the moment anything goes wrong. padErrorGo runs
  // exits/exit.php directly - no message, no log, no dump, and none of padExit's normal
  // header and output work.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    include PAD . 'exits/exit.php';

  }

?>