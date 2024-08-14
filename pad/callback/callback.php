<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include pad . 'callback/getCallback.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include pad . 'callback/getCallback.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'error/catch/callback.php';

  }

  restore_error_handler ();

?>