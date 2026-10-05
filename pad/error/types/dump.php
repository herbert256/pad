<?php

  // $padErrorAction 'dump': record and carry on. Each error is written as a full dump tree
  // under DATA by padDumpToDir (pad/lib/dump.php); clearing $padDumpToDirDone afterwards makes
  // the next error open its own directory instead of appending to this one.
  //
  // The clearing goes through $GLOBALS: an unset() of a name made global only drops the
  // function's own reference to it, so the guard stayed set and every later error of the
  // request got a directory holding an error.txt and nothing more.
  //
  // padErrorGo returns '' rather than exiting, so the request keeps running.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    padEventError ( $error, $file, $line );

    padDumpToDir ( "$file:$line $error" );
    padLogError  ( "$file:$line $error", 4 );

    unset ( $GLOBALS ['padDumpToDirDone'] );

    return '';

  }

?>