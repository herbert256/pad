<?php

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    padLogError ( "$file:$line $error", 4 );

    return '';

  }

?>