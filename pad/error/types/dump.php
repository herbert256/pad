<?php

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    global $padDumpToDirDone;

    padDumpToDir ( "$file:$line $error" );

    unset ( $padDumpToDirDone );

    return '';

  }

?>