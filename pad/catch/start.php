<?php

  if ( ! $padCatch )
    include 'start/enter/start.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include 'start/enter/start.php';

  } catch ( Throwable $padCatchException ) {

    include 'catch/catch/start.php';

  }

  restore_error_handler ();

?>