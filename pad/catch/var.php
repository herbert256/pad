<?php

  if ( ! $padCatch )
    return include PAD . 'level/var.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include PAD . 'level/var.php';

  } catch ( Throwable $padCatchException ) {

    include PAD . 'catch/catch/var.php';

  }

  restore_error_handler ();

?>