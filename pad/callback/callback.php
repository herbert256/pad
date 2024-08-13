<?php

  if ( ! $padCatch )
    return include pad . 'callback/getCallback.php';

  set_error_handler ( 'padThrow' );

  try {

    include pad . 'callback/getCallback.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'catch/callback.php';

  }

  restore_error_handler ();

?>