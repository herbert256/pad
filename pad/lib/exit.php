<?php

  function padExit ( $stop = 200 ) {

    global $exit;

    set_error_handler ( 'padErrorThrow' );

    try {

      padExitTry ( $stop );

    } catch (Throwable $e) {

      $exit = $e;

      padExitCatch ( $e );

    }

    restore_error_handler ();

    include PAD . 'exits/exit.php';

  }

  function padExitTry ( $stop ) {

    global $padInfoStarted, $padOutputType;

    if ( padSecondTime ( 'exit' ) )
      return padExitDouble ( $stop );

    padCloseSession ();

    padEmptyBuffers ( $padIgnored );

    if ( $padOutputType == 'web' )
      padWebHeaders ( $stop );

    if ( isset ( $padInfoStarted ) and ! padSecondTime ( 'exitInfo' ) )
      include PAD . 'info/end/config.php';

  }

  function padExitCatch ( $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorGo ( $e->getMessage(), $e->getFile(), $e->getLine() );

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }

  function padExitDouble ( $stop ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padSecondTime ( 'exitDouble' ) )
        include PAD . 'exits/exit.php';

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }

?>