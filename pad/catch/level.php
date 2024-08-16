<?php

  if ( ! $padCatch )
    return include pad . 'level/level.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include pad . 'level/level.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'catch/catch/level.php';

  }

  restore_error_handler ();

?>