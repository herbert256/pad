<?php

  if ( ! $padCatch )
    return include pad . 'build/getBuild.php';

  set_error_handler ( 'padThrow' );

  try {

    include pad . 'build/getBuild.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'catch/build.php';

  }

  restore_error_handler ();

?>