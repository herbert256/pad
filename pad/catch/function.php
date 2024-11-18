<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include PAD . 'start/function.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    $padCatchReturn = include PAD . 'start/function.php';

  } catch ( Throwable $padCatchException ) {

    $padCatchReturn = include PAD . 'catch/catch/function.php';

  }

  restore_error_handler ();

  return $padCatchReturn;

?>