<?php

  // $padErrorAction 'dump': record and carry on. Each error is written as a full dump tree
  // under DATA by padDumpToDir (pad/lib/dump.php); clearing $padDumpToDirDone afterwards makes
  // the next error open its own directory instead of appending to this one.
  //
  // padErrorGo returns '' rather than exiting, so the request keeps running.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    global $padDumpToDirDone;

    padDumpToDir ( "$file:$line $error" );

    unset ( $padDumpToDirDone );

    return '';

  }

?>