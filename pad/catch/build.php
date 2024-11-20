<?php

  if ( ! $padCatch )
    return include 'build/build.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include 'build/build.php';

  } catch ( Throwable $padCatchException ) {

    include 'catch/catch/build.php';

  }

  restore_error_handler ();

?>