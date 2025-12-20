<?php

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    padDumpToDir ( "$file:$line $error" );

    unset ( $GLOBALS ['padDumpToDirDone'] );

    return '';

  }

?>
