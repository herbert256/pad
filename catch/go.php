<?php

  if ( ! $padCatch )
    return include '/pad/level/go.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include '/pad/level/go.php';

  } catch ( Throwable $padCatchException ) {

    include '/pad/catch/catch/tag.php';

  }

  restore_error_handler ();

?>