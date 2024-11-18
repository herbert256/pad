<?php

  if ( ! $padCatch )
    return include PAD . 'level/go.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include PAD . 'level/go.php';

  } catch ( Throwable $padCatchException ) {

    include PAD . 'catch/catch/tag.php';

  }

  restore_error_handler ();

?>