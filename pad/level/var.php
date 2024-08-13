<?php

  if ( ! $padCatch )
    return include pad . 'level/getVar.php';

  set_error_handler ( 'padThrow' );

  try {

    include pad . 'level/getVar.php';

  } catch ( Throwable $padCatchException ) {

    include pad . 'catch/var.php';

  }

  restore_error_handler ();

?>