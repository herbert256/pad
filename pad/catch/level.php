<?php

  if ( ! $padCatch )
    return include 'level/level.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include 'level/level.php';

  } catch ( Throwable $padCatchException ) {

    include 'catch/catch/level.php';

  }

  restore_error_handler ();

?>