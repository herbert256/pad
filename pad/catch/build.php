<?php

  if ( ! $padCatch )
    return include PAD . 'build/build.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include PAD . 'build/build.php';

  } catch ( Throwable $padCatchException ) {

    include PAD . 'catch/catch/build.php';

  }

  restore_error_handler ();

?>