<?php

  if ( ! $padCatch )
    return include pad . 'level/var.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include pad . 'level/var.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'error/catch/var.php';

  }

  restore_error_handler ();

?>