<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include 'callback/callback.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include 'callback/callback.php';

  } catch ( Throwable $padCatchException ) {

    include 'catch/catch/callback.php';

  }

  restore_error_handler ();

?>