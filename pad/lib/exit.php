<?php


  /**
   * Terminates PAD execution gracefully.
   *
   * Closes the session, clears output buffers, sends headers,
   * runs info/end scripts, and then terminates. Handles errors
   * during exit gracefully.
   *
   * @param int $stop HTTP status code (default 200).
   *
   * @return void Never returns - script terminates.
   */
  function padExit ( $stop = 200 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padExitTry ( $stop );

    } catch (Throwable $e) {

      $GLOBALS['exit'] = $e;

      padExitCatch ( $e );

    }

    restore_error_handler ();

    include PAD . 'exits/exit.php';

  }


  /**
   * Main exit processing logic.
   *
   * Closes session, empties output buffers, sends HTTP headers,
   * and runs info/end scripts. Delegates to padExitDouble if
   * this is a recursive exit call.
   *
   * @param int $stop HTTP status code.
   *
   * @return void
   */
  function padExitTry ( $stop ) {

    if ( padSecondTime ( 'exit' ) )
      return padExitDouble ( $stop );

    padCloseSession ();

    padEmptyBuffers ( $padIgnored );

    if ( $GLOBALS ['padOutputType'] == 'web' )
      padWebHeaders ( $stop );

    if ( isset ( $GLOBALS ['padInfoStarted'] ) and ! padSecondTime ( 'exitInfo' ) )
      include PAD . 'info/end/config.php';

  }


  /**
   * Handles exceptions during exit processing.
   *
   * Attempts to log the error via padErrorGo. Silently ignores
   * any errors that occur during error handling itself.
   *
   * @param Throwable $e The exception that occurred during exit.
   *
   * @return void
   */
  function padExitCatch ( $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorGo ( $e->getMessage(), $e->getFile(), $e->getLine() );

    } catch (Throwable $e) {

      // Ignore errors

    }

    restore_error_handler ();

  }


  /**
   * Handles recursive/double exit calls.
   *
   * When padExit is called while already exiting, this function
   * handles the situation gracefully. On third exit attempt,
   * forces immediate termination.
   *
   * @param int $stop HTTP status code.
   *
   * @return void
   */
  function padExitDouble ( $stop ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padSecondTime ( 'exitDouble' ) )
        include PAD . 'exits/exit.php';

    } catch (Throwable $e) {

      // Ignore errors

    }

    restore_error_handler ();

  }


?>