<?php

  // The editor's debugger: the program Xdebug talks to. A browser cannot listen on a port,
  // so this one does, on the command line, in the background - started by the editor
  // (_lib/debug.php) and asked by it, one short connection per question:
  //
  //   php debugger.php <state directory> <port>
  //
  // It listens where Xdebug connects to, port 9003 on this machine's loopback addresses
  // (xdebug.client_port), and, on a port of its own, for the editor: a line of JSON with
  // the token written to control.json (mode 0600) in the state directory, answered with a
  // line of JSON. A request Xdebug starts for - one that carries XDEBUG_SESSION - is held
  // at its breakpoints; the editor's commands step it on, and at every stop the stack and
  // the variables are fetched before anyone asks, so the editor gets them with the state.
  //
  // One request at a time is debugged: a second one that arrives meanwhile is let go at
  // once (detach) and runs as if no debugger were there; so is a request of the editor
  // itself. The editor asks for the state with a sequence number and is answered as soon
  // as the state is newer, or after eight seconds - a long poll. Unasked for twenty
  // minutes and with no request held, the debugger ends itself.
  //
  // Commands from the editor:
  //   state {since}                  the state, once it is newer than since
  //   breakpoints {list, exceptions, first}
  //                                  where to stop - files and lines, with conditions - and
  //                                  whether at exceptions, and at a request's first line
  //   run, step_over, step_into, step_out, stop, detach
  //   eval {expression, depth}, property {name, depth, context, page}, frame {depth},
  //   source {file}                  questions about the paused request
  //   quit

  require __DIR__ . '/../_lib/dbgp.php';

  error_reporting ( E_ALL );
  set_time_limit ( 0 );

  $dir  = rtrim ( (string) ( $argv [1] ?? '' ), '/' ) . '/';
  $port = (int) ( $argv [2] ?? 9003 );

  if ( ! is_dir ( $dir ) ) {
    fwrite ( STDERR, "debugger: no state directory\n" );
    exit ( 2 );
  }

  $listen = [];
  $why    = '';

  foreach ( [ "tcp://127.0.0.1:$port", "tcp://[::1]:$port" ] as $address ) {
    $socket = @stream_socket_server ( $address, $errno, $errstr );
    if ( $socket )
      $listen [] = $socket;
    else
      $why = $errstr;
  }

  if ( ! $listen ) {
    file_put_contents ( $dir . 'error', "port $port is not free - another debugger listens there? ($why)" );
    exit ( 1 );
  }

  $control = stream_socket_server ( 'tcp://127.0.0.1:0', $errno, $errstr );
  $token   = bin2hex ( random_bytes ( 16 ) );
  $cport   = (int) substr ( strrchr ( stream_socket_get_name ( $control, FALSE ), ':' ), 1 );

  file_put_contents ( $dir . 'control.json', json_encode ( [ 'port' => $cport, 'token' => $token, 'pid' => getmypid (), 'xdebug' => $port ] ) );
  chmod ( $dir . 'control.json', 0600 );

  $S = [ 'seq' => 1, 'breaks' => 0, 'status' => 'listening', 'session' => NULL, 'location' => NULL, 'stack' => [], 'locals' => [],
         'globals' => [], 'message' => '', 'log' => [], 'port' => $port ];

  $want = [ 'list' => [], 'exceptions' => FALSE, 'first' => FALSE ];   // what the editor asked for
  $set  = [];                                                           // what Xdebug has: key => id

  $xd       = NULL;    // the request being debugged
  $xdBuffer = '';
  $tid      = 0;
  $pending  = [];      // transaction id => callback
  $others   = [];      // requests being let go: [ socket, buffer ]
  $clients  = [];      // editor connections: id => [ socket, buffer ]
  $waiters  = [];      // editor connections waiting for a newer state: [ socket, since, until ]
  $asked    = time ();
  $fetching = 0;

  function dbgLog ( $text ) {

    global $S;

    $S ['log'] [] = date ( 'H:i:s' ) . ' ' . $text;
    $S ['log']    = array_slice ( $S ['log'], -50 );

  }

  function dbgChanged () {

    global $S;

    $S ['seq']++;

  }

  function dbgSend ( $name, $args = [], $data = NULL, $callback = NULL ) {

    global $xd, $tid, $pending;

    if ( ! $xd )
      return FALSE;

    $tid++;
    $pending [$tid] = $callback;

    @fwrite ( $xd, dbgpCommand ( $name, $tid, $args, $data ) );

    return TRUE;

  }

  function dbgReply ( $socket, $answer ) {

    @fwrite ( $socket, json_encode ( $answer, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE ) . "\n" );
    @fclose ( $socket );

  }

  // Xdebug gets the breakpoints the editor wants and loses the ones it no longer does - at
  // a request's start and at every stop: while a request runs, Xdebug reads no commands.

  function dbgSync () {

    global $want, $set;

    $keep = [];

    foreach ( $want ['list'] as $one ) {

      $key = $one ['file'] . ':' . $one ['line'] . ':' . ( $one ['condition'] ?? '' );
      $keep [$key] = TRUE;

      if ( isset ( $set [$key] ) )
        continue;

      $set [$key] = 0;

      $args = [ 't' => ( $one ['condition'] ?? '' ) !== '' ? 'conditional' : 'line', 'f' => dbgpUri ( $one ['file'] ), 'n' => (int) $one ['line'] ];

      dbgSend ( 'breakpoint_set', $args, ( $one ['condition'] ?? '' ) !== '' ? $one ['condition'] : NULL,
                function ( $r ) use ( $key ) { global $set; $set [$key] = (int) ( $r ['id'] ?? 0 ); } );

    }

    if ( $want ['exceptions'] ) {
      $keep ['exception'] = TRUE;
      if ( ! isset ( $set ['exception'] ) ) {
        $set ['exception'] = 0;
        dbgSend ( 'breakpoint_set', [ 't' => 'exception', 'x' => '*' ], NULL,
                  function ( $r ) { global $set; $set ['exception'] = (int) ( $r ['id'] ?? 0 ); } );
      }
    }

    foreach ( $set as $key => $id )
      if ( ! isset ( $keep [$key] ) ) {
        if ( $id )
          dbgSend ( 'breakpoint_remove', [ 'd' => $id ] );
        unset ( $set [$key] );
      }

  }

  // A run or a step came back: stopped somewhere - the stack and the variables are fetched
  // at once - or at the end of the request.

  function dbgMoved ( $response ) {

    global $S, $fetching;

    $status = (string) ( $response ['status'] ?? '' );
    $error  = dbgpError ( $response );

    if ( $error !== '' ) {
      $S ['message'] = $error;
      dbgLog ( $error );
    }

    if ( $status === 'break' ) {

      $S ['status']   = 'break';
      $S ['breaks']++;
      $S ['location'] = dbgpLocation ( $response );
      $S ['message']  = ( $S ['location'] ['exception'] ?? '' ) !== '' ? $S ['location'] ['exception'] . ': ' . $S ['location'] ['text'] : '';

      dbgSync ();

      $fetching = 3;

      dbgSend ( 'stack_get', [], NULL, function ( $r ) {
        global $S;
        $S ['stack'] = dbgpStack ( $r );
        if ( ! $S ['location'] and $S ['stack'] )
          $S ['location'] = [ 'file' => $S ['stack'] [0] ['file'], 'line' => $S ['stack'] [0] ['line'], 'exception' => '', 'text' => '' ];
        dbgFetched ();
      } );

      foreach ( [ 0 => 'locals', 1 => 'globals' ] as $context => $key )
        dbgSend ( 'context_get', [ 'd' => 0, 'c' => $context ], NULL, function ( $r ) use ( $key ) {
          global $S;
          $S [$key] = [];
          foreach ( $r->property ?? [] as $p )
            $S [$key] [] = dbgpProperty ( $p, 300 );
          dbgFetched ();
        } );

      return;

    }

    if ( $status === 'stopping' or $status === 'stopped' ) {
      dbgLog ( 'the request ended' );
      dbgSend ( 'stop' );
      dbgEnd ();
      return;
    }

    $S ['status'] = $status ?: $S ['status'];
    dbgChanged ();

  }

  function dbgFetched () {

    global $fetching;

    if ( --$fetching <= 0 )
      dbgChanged ();

  }

  function dbgEnd () {

    global $xd, $xdBuffer, $pending, $set, $S;

    if ( $xd )
      @fclose ( $xd );

    $xd       = NULL;
    $xdBuffer = '';
    $pending  = [];
    $set      = [];

    $S ['status']   = 'listening';
    $S ['session']  = NULL;
    $S ['location'] = NULL;
    $S ['stack']    = [];
    $S ['locals']   = [];
    $S ['globals']  = [];

    dbgChanged ();

  }

  // A request has connected: told what to send - values short enough to show, one level
  // deep - given the breakpoints, and let run; or, with "stop at the first line", stepped
  // into its first line.

  function dbgInit ( $init ) {

    global $S, $want;

    $file = dbgpPath ( (string) $init ['fileuri'] );

    // A request of the editor itself is never held - its entry point says which it is.

    if ( str_contains ( $file, '/www/edit/' ) ) {
      dbgSend ( 'detach' );
      dbgEnd ();
      return;
    }

    $S ['session']  = [ 'file' => $file, 'since' => time (), 'idekey' => (string) $init ['idekey'] ];
    $S ['status']   = 'running';
    $S ['message']  = '';
    $S ['location'] = NULL;

    dbgLog ( 'a request connected: ' . $file );

    foreach ( [ 'max_depth' => 1, 'max_children' => 100, 'max_data' => 4096 ] as $feature => $value )
      dbgSend ( 'feature_set', [ 'n' => $feature, 'v' => $value ] );

    dbgSync ();

    dbgSend ( $want ['first'] ? 'step_into' : 'run', [], NULL, 'dbgMoved' );

    dbgChanged ();

  }

  function dbgFromXdebug ( $xml ) {

    global $pending, $xd;

    $packet = dbgpParse ( $xml );

    if ( ! $packet )
      return;

    $name = $packet->getName ();

    if ( $name === 'init' ) {
      dbgInit ( $packet );
      return;
    }

    if ( $name !== 'response' )
      return;

    $id       = (int) $packet ['transaction_id'];
    $callback = $pending [$id] ?? NULL;

    unset ( $pending [$id] );

    if ( $callback )
      $callback ( $packet );

  }

  // ---------------------------------------------------------------------------------------
  // The editor's commands
  // ---------------------------------------------------------------------------------------

  function dbgState () {

    global $S, $want;

    return [ 'ok' => TRUE ] + $S + [ 'breakpoints' => count ( $want ['list'] ), 'pid' => getmypid () ];

  }

  function dbgCommand ( $socket, $ask ) {

    global $S, $want, $waiters, $xd, $token;

    if ( ! is_array ( $ask ) or ! hash_equals ( $token, (string) ( $ask ['token'] ?? '' ) ) ) {
      dbgReply ( $socket, [ 'ok' => FALSE, 'error' => 'not the editor' ] );
      return;
    }

    $paused = ( $xd and $S ['status'] === 'break' );
    $notNow = [ 'ok' => FALSE, 'error' => 'the request is not paused' ];

    switch ( (string) ( $ask ['cmd'] ?? '' ) ) {

      case 'state':
        if ( (int) ( $ask ['since'] ?? 0 ) < $S ['seq'] )
          dbgReply ( $socket, dbgState () );
        else
          $waiters [] = [ $socket, (int) $ask ['since'], time () + 8 ];
        return;

      case 'breakpoints':
        $want = [ 'list' => array_values ( array_filter ( (array) ( $ask ['list'] ?? [] ), 'is_array' ) ),
                  'exceptions' => ! empty ( $ask ['exceptions'] ), 'first' => ! empty ( $ask ['first'] ) ];
        if ( $paused )
          dbgSync ();
        dbgChanged ();
        dbgReply ( $socket, dbgState () );
        return;

      case 'run':
      case 'step_over':
      case 'step_into':
      case 'step_out':
        if ( ! $paused ) {
          dbgReply ( $socket, $notNow );
          return;
        }
        $S ['status'] = 'running';
        $S ['message'] = '';
        dbgSend ( $ask ['cmd'], [], NULL, 'dbgMoved' );
        dbgChanged ();
        dbgReply ( $socket, [ 'ok' => TRUE ] );
        return;

      case 'stop':
      case 'detach':
        if ( $paused )
          dbgSend ( $ask ['cmd'] );
        if ( $xd ) {
          dbgLog ( $ask ['cmd'] == 'stop' ? 'the request was stopped' : 'the request was let go' );
          dbgEnd ();
        }
        dbgReply ( $socket, [ 'ok' => TRUE ] );
        return;

      case 'eval':
        if ( ! $paused ) {
          dbgReply ( $socket, $notNow );
          return;
        }
        dbgSend ( 'eval', [], (string) ( $ask ['expression'] ?? '' ), function ( $r ) use ( $socket ) {
          $error = dbgpError ( $r );
          dbgReply ( $socket, $error !== '' ? [ 'ok' => FALSE, 'error' => $error ]
                                            : [ 'ok' => TRUE, 'property' => isset ( $r->property ) ? dbgpProperty ( $r->property ) : NULL ] );
        } );
        return;

      case 'property':
        if ( ! $paused ) {
          dbgReply ( $socket, $notNow );
          return;
        }
        dbgSend ( 'property_get', [ 'n' => (string) ( $ask ['name'] ?? '' ), 'd' => (int) ( $ask ['depth'] ?? 0 ),
                                    'c' => (int) ( $ask ['context'] ?? 0 ), 'p' => (int) ( $ask ['page'] ?? 0 ) ], NULL,
                  function ( $r ) use ( $socket ) {
                    $error = dbgpError ( $r );
                    dbgReply ( $socket, $error !== '' ? [ 'ok' => FALSE, 'error' => $error ]
                                                      : [ 'ok' => TRUE, 'property' => isset ( $r->property ) ? dbgpProperty ( $r->property ) : NULL ] );
                  } );
        return;

      case 'frame':
        if ( ! $paused ) {
          dbgReply ( $socket, $notNow );
          return;
        }
        dbgSend ( 'context_get', [ 'd' => (int) ( $ask ['depth'] ?? 0 ), 'c' => 0 ], NULL, function ( $r ) use ( $socket ) {
          $list = [];
          foreach ( $r->property ?? [] as $p )
            $list [] = dbgpProperty ( $p, 300 );
          dbgReply ( $socket, [ 'ok' => TRUE, 'locals' => $list ] );
        } );
        return;

      case 'source':
        if ( ! $paused ) {
          dbgReply ( $socket, $notNow );
          return;
        }
        dbgSend ( 'source', [ 'f' => dbgpUri ( (string) ( $ask ['file'] ?? '' ) ) ], NULL, function ( $r ) use ( $socket ) {
          $error = dbgpError ( $r );
          dbgReply ( $socket, $error !== '' ? [ 'ok' => FALSE, 'error' => $error ]
                                            : [ 'ok' => TRUE, 'text' => (string) base64_decode ( (string) $r ) ] );
        } );
        return;

      case 'quit':
        dbgReply ( $socket, [ 'ok' => TRUE ] );
        if ( $xd and $paused )
          dbgSend ( 'detach' );
        exit ( 0 );

    }

    dbgReply ( $socket, [ 'ok' => FALSE, 'error' => 'there is no such command' ] );

  }

  // ---------------------------------------------------------------------------------------
  // The loop
  // ---------------------------------------------------------------------------------------

  while ( TRUE ) {

    $read = array_merge ( $listen, [ $control ] );

    if ( $xd )
      $read [] = $xd;

    foreach ( $others as $one )
      $read [] = $one [0];

    foreach ( $clients as $one )
      $read [] = $one [0];

    $write = $except = NULL;

    if ( @stream_select ( $read, $write, $except, 1 ) === FALSE )
      usleep ( 100000 );

    foreach ( $read as $socket ) {

      if ( in_array ( $socket, $listen, TRUE ) ) {

        $new = @stream_socket_accept ( $socket, 0 );

        if ( ! $new )
          continue;

        if ( $xd )
          $others [ (int) $new ] = [ $new, '' ];
        else {
          $xd       = $new;
          $xdBuffer = '';
          stream_set_blocking ( $xd, FALSE );
        }

        continue;

      }

      if ( $socket === $control ) {
        $new = @stream_socket_accept ( $control, 0 );
        if ( $new ) {
          stream_set_blocking ( $new, FALSE );
          $clients [ (int) $new ] = [ $new, '' ];
        }
        continue;
      }

      if ( $socket === $xd ) {

        $data = @fread ( $xd, 65536 );

        if ( $data === '' or $data === FALSE ) {
          if ( feof ( $xd ) ) {
            dbgLog ( 'the request disconnected' );
            dbgEnd ();
          }
          continue;
        }

        $xdBuffer .= $data;

        foreach ( dbgpFrames ( $xdBuffer ) as $xml )
          dbgFromXdebug ( $xml );

        continue;

      }

      if ( isset ( $others [ (int) $socket ] ) ) {

        // a second request: read its init, then let it go - or the editor's own request

        $key  = (int) $socket;
        $data = @fread ( $socket, 65536 );

        if ( $data === '' or $data === FALSE ) {
          @fclose ( $socket );
          unset ( $others [$key] );
          continue;
        }

        $others [$key] [1] .= $data;

        foreach ( dbgpFrames ( $others [$key] [1] ) as $xml ) {
          $init = dbgpParse ( $xml );
          if ( $init and $init->getName () === 'init' and ! str_contains ( (string) $init ['fileuri'], '/www/edit/' ) ) {
            dbgLog ( 'another request ran on without stopping: ' . dbgpPath ( (string) $init ['fileuri'] ) );
            dbgChanged ();
          }
          @fwrite ( $socket, dbgpCommand ( 'detach', 1 ) );
          @fclose ( $socket );
          unset ( $others [$key] );
          break;
        }

        continue;

      }

      if ( isset ( $clients [ (int) $socket ] ) ) {

        $key  = (int) $socket;
        $data = @fread ( $socket, 65536 );

        if ( $data === '' or $data === FALSE ) {
          if ( feof ( $socket ) ) {
            @fclose ( $socket );
            unset ( $clients [$key] );
          }
          continue;
        }

        $clients [$key] [1] .= $data;

        if ( ( $end = strpos ( $clients [$key] [1], "\n" ) ) !== FALSE ) {
          $line = substr ( $clients [$key] [1], 0, $end );
          unset ( $clients [$key] );
          stream_set_blocking ( $socket, TRUE );
          $asked = time ();
          dbgCommand ( $socket, json_decode ( $line, TRUE ) );
        }

      }

    }

    foreach ( $waiters as $i => [ $socket, $since, $until ] )
      if ( $since < $S ['seq'] or time () >= $until ) {
        dbgReply ( $socket, dbgState () );
        unset ( $waiters [$i] );
      }

    if ( ! $xd and ! $waiters and time () - $asked > 1200 )
      exit ( 0 );

  }

?>
