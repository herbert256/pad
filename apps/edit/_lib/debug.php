<?php

  // The step debugger, from the editor's side: Xdebug in the request being debugged talks
  // to the debugger process (_bin/debugger.php), and this file starts that process, asks it
  // what it knows and passes the editor's commands on. A request is debugged when it
  // carries XDEBUG_SESSION - the preview's Debug button adds it to the address, and a
  // cookie can carry it for every request of one application.
  //
  // editDebugInfo     whether this PHP has Xdebug, in its debug mode, and on which port it
  //                   connects back - xdebug.client_port
  // editDebugDir      the debugger's state directory, beside the terminal's jobs (0700)
  // editDebugRunning  whether the debugger process is there
  // editDebugStart    starts it, unless it runs; refused when the port is taken
  // editDebugAsk      one command to it: a line of JSON with its token, one line back
  // editDebugStop     ends it

  function editDebugInfo () {

    $loaded = extension_loaded ( 'xdebug' );
    $mode   = $loaded ? (string) ini_get ( 'xdebug.mode' ) : '';

    return [ 'loaded'  => $loaded,
             'version' => $loaded ? (string) phpversion ( 'xdebug' ) : '',
             'mode'    => $mode,
             'debug'   => in_array ( 'debug', array_map ( 'trim', explode ( ',', $mode ) ), TRUE ),
             'start'   => $loaded ? (string) ini_get ( 'xdebug.start_with_request' ) : '',
             'port'    => (int) ( ( $loaded ? ini_get ( 'xdebug.client_port' ) : 0 ) ?: 9003 ),
             'host'    => $loaded ? (string) ini_get ( 'xdebug.client_host' ) : '' ];

  }

  function editDebugDir ( $base = NULL ) {

    $dir = ( $base ?? editTermBase () ) . 'debug/';

    if ( ! is_dir ( $dir ) )
      @mkdir ( $dir, 0700, TRUE );

    return $dir;

  }

  function editDebugControl ( $dir ) {

    $control = json_decode ( (string) @file_get_contents ( $dir . 'control.json' ), TRUE );

    return ( is_array ( $control ) and editTermAlive ( (int) ( $control ['pid'] ?? 0 ) ) ) ? $control : NULL;

  }

  function editDebugRunning ( $dir ) {

    return editDebugControl ( $dir ) !== NULL;

  }

  // The process is started like a terminal job - in the background, in a process group of
  // its own, so it outlives the request - with Xdebug switched off for itself.

  function editDebugStart ( $dir, $port ) {

    if ( editDebugRunning ( $dir ) )
      return editDebugControl ( $dir );

    @unlink ( $dir . 'control.json' );
    @unlink ( $dir . 'error' );

    $launch = 'set -m; ' . escapeshellarg ( editPhpBinary () ) . ' -d xdebug.mode=off '
            . escapeshellarg ( APP . '_bin/debugger.php' ) . ' ' . escapeshellarg ( $dir ) . ' ' . (int) $port
            . ' > ' . escapeshellarg ( $dir . 'log' ) . ' 2>&1 < /dev/null & echo $!';

    $proc = proc_open ( [ '/bin/sh', '-c', $launch ], [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $pipes, $dir );

    if ( ! is_resource ( $proc ) )
      editFail ( 'the debugger could not be started' );

    stream_get_contents ( $pipes [1] );
    fclose ( $pipes [1] );
    proc_close ( $proc );

    for ( $wait = 0; $wait < 60; $wait++ ) {
      clearstatcache ();
      if ( is_file ( $dir . 'error' ) )
        editFail ( 'the debugger could not start: ' . trim ( (string) file_get_contents ( $dir . 'error' ) ) );
      if ( $control = editDebugControl ( $dir ) )
        return $control;
      usleep ( 50000 );
    }

    editFail ( 'the debugger did not start: ' . trim ( (string) @file_get_contents ( $dir . 'log' ) ) );

  }

  function editDebugAsk ( $dir, $command, $timeout = 12 ) {

    $control = editDebugControl ( $dir );

    if ( ! $control )
      return [ 'ok' => FALSE, 'error' => 'the debugger is not running', 'off' => TRUE ];

    $socket = @stream_socket_client ( 'tcp://127.0.0.1:' . (int) $control ['port'], $errno, $errstr, 3 );

    if ( ! $socket )
      return [ 'ok' => FALSE, 'error' => 'the debugger does not answer', 'off' => TRUE ];

    stream_set_timeout ( $socket, $timeout );

    fwrite ( $socket, json_encode ( $command + [ 'token' => $control ['token'] ] ) . "\n" );

    $line = fgets ( $socket );

    fclose ( $socket );

    $answer = json_decode ( (string) $line, TRUE );

    return is_array ( $answer ) ? $answer : [ 'ok' => FALSE, 'error' => 'the debugger gave no answer' ];

  }

  function editDebugStop ( $dir ) {

    $control = editDebugControl ( $dir );

    if ( ! $control )
      return;

    editDebugAsk ( $dir, [ 'cmd' => 'quit' ], 3 );

    for ( $wait = 0; $wait < 20 and editTermAlive ( (int) $control ['pid'] ); $wait++ )
      usleep ( 50000 );

    if ( editTermAlive ( (int) $control ['pid'] ) and function_exists ( 'posix_kill' ) )
      posix_kill ( (int) $control ['pid'], 9 );

    @unlink ( $dir . 'control.json' );

  }

?>
