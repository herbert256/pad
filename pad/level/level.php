<?php

  if ( ! $padCatch )
    return include pad . 'level/getLevel.php';

  set_error_handler ( 'padThrow' );

  try {

    include pad . 'level/getLevel.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'catch/level.php';

  }

  restore_error_handler ();

?>