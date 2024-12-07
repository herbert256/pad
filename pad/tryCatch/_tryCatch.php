<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include "tryCatch/try/$padTryCatch.php";

  set_error_handler ( 'padErrorThrow' );

  try {

    $padTryCatchReturn = include "tryCatch/try/$padTryCatch.php";

  } catch ( Throwable $padException ) {

    $padTryCatchReturn = include 'tryCatch/_catch.php';

  }

  restore_error_handler ();

  return $padTryCatchReturn;

?>