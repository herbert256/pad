<?php

  if ( ! $padCatch )
    return include PAD . 'level/level.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include PAD . 'level/level.php';

  } catch ( Throwable $padCatchException ) {

    include PAD . 'catch/catch/level.php';

  }

  restore_error_handler ();

?>