<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include pad . 'start/function.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    $padCatchReturn = include pad . 'start/function.php';

  } catch ( Throwable $padCatchException ) {

    $padCatchReturn = include pad . 'catch/catch/function.php';

  }

  restore_error_handler ();

  return $padCatchReturn;

?>