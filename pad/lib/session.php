<?php

  function padInfo () {

    global $padEtag, $padLen, $padPage, $padRefID, $padReqID, $padSesID, $padStop;

    return [
      'session' => $padSesID ?? '',
      'request' => $padReqID ?? '',
      'parent'  => $padRefID ?? '',
      'page'    => $padPage  ?? '',
      'stop'    => $padStop  ?? '',
      'length'  => $padLen   ?? '',
      'start'   => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0 ,
      'end'     => microtime (true),
      'etag'    => $padEtag  ?? ''
    ];

  }

  function padInclude () {

    global $padInclude;

    if ( isset ( $padInclude ) and $padInclude )
      return TRUE;
    else
      return FALSE;

  }

  function padSecondTime ( $id ) {

    if ( isset ( $GLOBALS ["padSecond$id"] ) )
      return TRUE;

    $GLOBALS ["padSecond$id"] = TRUE;

    return FALSE;

  }

  function padCloseSession () {

    set_error_handler ( 'padErrorThrow' );

    try {

      padCloseSessionTry ();

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }

  function padCloseSessionTry () {

    if ( ! isset ( $GLOBALS ['padSessionStarted'] ) or padSecondTime ( 'closeSession' ) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }

  function padID () {

    global $padReqID;

    return $padReqID ?? uniqid (TRUE);

  }

  function padLogError ( $error ) {

    error_log ( '[PAD] ' . padID () . ' ' . padMakeSafe ( $error ), 4 );

  }

?>
