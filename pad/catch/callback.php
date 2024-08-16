<?php

  if ( ! $GLOBALS ['padCatch'] )
    return include pad . 'callback/callback.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include pad . 'callback/callback.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'catch/catch/callback.php';

  }

  restore_error_handler ();

?>