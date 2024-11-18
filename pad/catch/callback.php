<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include PAD . 'callback/callback.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include PAD . 'callback/callback.php';

  } catch ( Throwable $padCatchException ) {

    include PAD . 'catch/catch/callback.php';

  }

  restore_error_handler ();

?>