<?php

  if ( ! $padCatch )
    include '/pad/start/enter/start.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include '/pad/start/enter/start.php';

  } catch ( Throwable $padCatchException ) {

    include '/pad/catch/catch/start.php';

  }

  restore_error_handler ();

?>