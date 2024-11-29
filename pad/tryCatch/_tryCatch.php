<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include "tryCatch/try/$padTryCatch.php";

  set_error_handler ( 'padErrorThrow' );

  try {

    $padTryCatchReturn = include "tryCatch/try/$padTryCatch.php";

  } catch ( Throwable $padException ) {

    include 'tryCatch/_catch.php';

    $padTryCatchReturn = include "tryCatch/catch/$padTryCatch.php";;

  }

  restore_error_handler ();

  return $padTryCatchReturn;

?>