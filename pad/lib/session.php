<?php


  /**
   * Returns current request information array.
   *
   * @return array Request info with session, request, parent, page, stop, length, start, end, etag.
   */
  function padInfo () {

    return [
      'session' => $GLOBALS ['padSesID'] ?? '',
      'request' => $GLOBALS ['padReqID'] ?? '',
      'parent'  => $GLOBALS ['padRefID'] ?? '',
      'page'    => $GLOBALS ['padPage']  ?? '',
      'stop'    => $GLOBALS ['padStop']  ?? '',
      'length'  => $GLOBALS ['padLen']   ?? '',
      'start'   => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0 ,
      'end'     => microtime (true),
      'etag'    => $GLOBALS ['padEtag']  ?? ''
    ];

  }


  /**
   * Checks if current request is an include/embedded request.
   *
   * @return bool TRUE if padInclude flag is set.
   */
  function padInclude () {

    if ( isset ( $GLOBALS ['padInclude'] ) and $GLOBALS ['padInclude'] )
      return TRUE;
    else
      return FALSE;

  }


  /**
   * Checks if this is the second call with given ID.
   *
   * Returns FALSE on first call, TRUE on subsequent calls.
   *
   * @param string $id Unique identifier for the check.
   *
   * @return bool TRUE if called before with same ID.
   */
  function padSecondTime ( $id ) {

    if ( isset ( $GLOBALS ["padSecond$id"] ) )
      return TRUE;

    $GLOBALS ["padSecond$id"] = TRUE;

    return FALSE;

  }


  /**
   * Closes the PHP session safely with error handling.
   *
   * @return void
   */
  function padCloseSession () {

    set_error_handler ( 'padErrorThrow' );

    try {

      padCloseSessionTry ();

    } catch (Throwable $e) {

      // Ignore errors

    }

    restore_error_handler ();

  }


  /**
   * Internal session close logic.
   *
   * Saves session variables and writes session data.
   *
   * @return void
   */
  function padCloseSessionTry () {

    if ( ! isset ( $GLOBALS ['padSessionStarted'] ) or padSecondTime ( 'closeSession' ) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }


  /**
   * Returns current request ID or generates unique ID.
   *
   * @return string Request ID.
   */
  function padID () {

    return $GLOBALS ['padReqID'] ?? uniqid (TRUE);

  }


  /**
   * Logs error message to PHP error log.
   *
   * @param string $error Error message.
   *
   * @return void
   */
  function padLogError ( $error ) {

    error_log ( '[PAD] ' . padID () . ' ' . padMakeSafe ( $error ), 4 );

  }


?>
