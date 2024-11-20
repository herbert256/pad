<?php

  if ( ! $padCatch )
    return include 'level/var.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include 'level/var.php';

  } catch ( Throwable $padCatchException ) {

    include 'catch/catch/var.php';

  }

  restore_error_handler ();

?>