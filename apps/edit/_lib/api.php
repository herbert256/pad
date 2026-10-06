<?php

  // Who may reach the editor, and how its JSON calls run.
  //
  // editAllowed  the machine rule of _guard.php: the command line always; a web request only
  //              from this machine - the loopback address, nothing forwarded (padLoopback) -
  //              naming this machine in its Host header, unless $editRemote opens it up.
  //              The Host check is what keeps a page on another site out that has its own
  //              name resolve to 127.0.0.1 (DNS rebinding): its requests reach the loopback
  //              address, but say Host: that-site.example.
  // editApi      runs one call of api.php: the session is closed first - written back and
  //              unlocked - so a slow check or search does not hold up the next call; then
  //              the call's file in _api/ runs in a function of its own, with every PHP
  //              warning turned into an exception, so whatever goes wrong comes back as the
  //              call's error in the JSON answer, never as PAD's HTML error page
  // editBody     the call's arguments: the JSON body as it was sent - not trimmed, a file's
  //              whitespace is its own - or, for an upload, the form fields

  function editAllowed () {

    global $editRemote, $editHosts;

    if ( PHP_SAPI === 'cli' )
      return TRUE;

    if ( $editRemote )
      return ! $editHosts or editHostNamed ( (array) $editHosts );

    return padLoopback () and editHostNamed ( array_merge ( [ 'localhost', '127.0.0.1', '[::1]', '::1' ], (array) $editHosts ) );

  }

  function editHostNamed ( $hosts ) {

    $host = strtolower ( trim ( (string) ( $_SERVER ['HTTP_HOST'] ?? '' ) ) );
    $host = preg_replace ( '/:\d+$/', '', $host );

    return in_array ( $host, array_map ( 'strtolower', $hosts ), TRUE );

  }

  function editApi ( $action ) {

    if ( $action != 'users' )
      padCloseSession ();

    set_error_handler ( function ( $number, $message, $file, $line ) {
      if ( ! ( error_reporting () & $number ) )
        return TRUE;
      throw new ErrorException ( $message, 0, $number, $file, $line );
    } );

    try {

      $body = editBody ();
      $data = ( function ( $body ) use ( $action ) { return include APP . "_api/$action.php"; } ) ( $body );

      $answer = [ TRUE, '', $data ];

    } catch ( RuntimeException $e ) {

      $answer = [ FALSE, $e->getMessage (), NULL ];

    } catch ( Throwable $e ) {

      $answer = [ FALSE, $e->getMessage () . ' (' . basename ( $e->getFile () ) . ':' . $e->getLine () . ')', NULL ];

    } finally {

      restore_error_handler ();

    }

    return $answer;

  }

  function editBody () {

    static $body = NULL;

    if ( $body !== NULL )
      return $body;

    $type   = strtolower ( (string) ( $_SERVER ['CONTENT_TYPE'] ?? '' ) );
    $length = (int) ( $_SERVER ['CONTENT_LENGTH'] ?? 0 );

    if ( str_starts_with ( $type, 'multipart/form-data' ) ) {
      if ( ! $_POST and ! $_FILES and $length > 0 )
        editFail ( 'the upload is larger than this server takes: post_max_size is ' . ini_get ( 'post_max_size' ) );
      return $body = $_POST;
    }

    $raw = (string) file_get_contents ( 'php://input' );

    if ( $raw === '' and $length > 0 )
      editFail ( 'the request is larger than this server takes: post_max_size is ' . ini_get ( 'post_max_size' ) );

    $body = ( $raw === '' ) ? [] : json_decode ( $raw, TRUE );

    if ( ! is_array ( $body ) )
      editFail ( 'the request is no JSON object' );

    return $body;

  }

  function editArg ( $body, $key, $default = '' ) {

    $value = $body [$key] ?? $default;

    return is_scalar ( $value ) ? (string) $value : $default;

  }

  // The file a call is about: [ app, root, relative path, absolute path ].

  function editTarget ( $body, $key = 'path', $rootKey = 'root' ) {

    $app  = editApp ( editArg ( $body, 'app' ) );
    $root = editArg ( $body, $rootKey, 'app' );
    $rel  = trim ( editArg ( $body, $key ), '/' );

    return [ $app, $root, $rel, editPath ( $app, $root, $rel ) ];

  }

  // A php.ini size - 2M, 8M, 1G - in bytes.

  function editIniBytes ( $name ) {

    $value = trim ( (string) ini_get ( $name ) );
    $last  = strtolower ( substr ( $value, -1 ) );
    $bytes = (int) $value;

    return match ( $last ) {
      'g'     => $bytes * 1073741824,
      'm'     => $bytes * 1048576,
      'k'     => $bytes * 1024,
      default => $bytes
    };

  }

?>
