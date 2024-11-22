<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include 'start/function.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    $padCatchReturn = include 'start/function.php';

  } catch ( Throwable $padCatchException ) {

    $padCatchReturn = include 'catch/catch/function.php';

  }

  restore_error_handler ();

  return $padCatchReturn;

?>