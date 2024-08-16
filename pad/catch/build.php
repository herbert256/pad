<?php

  if ( ! $padCatch )
    return include pad . 'build/build.php';

  set_error_handler ( 'padErrorThrow' );

  try {

    include pad . 'build/build.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'catch/catch/build.php';

  }

  restore_error_handler ();

?>