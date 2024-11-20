<?php

  if ( ! $padCatch )
    return include 'level/go.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include 'level/go.php';

  } catch ( Throwable $padCatchException ) {

    include 'catch/catch/tag.php';

  }

  restore_error_handler ();

?>