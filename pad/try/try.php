<?php 

  if ( ! $GLOBALS ['padTryCatch'] )
    return include "$padTry.php";

  set_error_handler ( 'padErrorThrow' );

  try {

    $padTryReturn = include "$padTry.php";

  } catch (Throwable $padTryException) {

    $padTryReturn = include "try/$padTry.php";

  }

  restore_error_handler ();

  return $padTryReturn;

?>