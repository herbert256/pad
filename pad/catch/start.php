<?php

  if ( ! $padCatch )
    include PAD . 'start/enter/start.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include PAD . 'start/enter/start.php';

  } catch ( Throwable $padCatchException ) {

    include PAD . 'catch/catch/start.php';

  }

  restore_error_handler ();

?>