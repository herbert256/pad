<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include "tryCatch/try/$tryCatch.php";

  set_error_handler ( 'padErrorThrow' );

  try {

    $padCatchReturn = include "tryCatch/try/$tryCatch.php";

  } catch ( Throwable $padCatchException ) {

    $padCatchReturn = include "tryCatch/catch/$tryCatch.php";;

  }

  restore_error_handler ();

  return $padCatchReturn;

?>