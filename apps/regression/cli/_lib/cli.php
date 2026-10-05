<?php

  // Runs the pad command - apps/cli/pad - with $args in a child process, with the
  // environment plus $env, and returns [ exit code, stdout ]. The php binary is the one
  // running this page when that is the command-line one (php -S), else the one beside it.

  function cliCheckRun ( $args, $env = [] ) {

    $php = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';

    $proc = proc_open ( array_merge ( [ $php, dirname ( APPS ) . '/apps/cli/pad' ], $args ),
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
                        $pipes, NULL, array_merge ( getenv (), $env ) );

    if ( ! is_resource ( $proc ) )
      return [ -1, '' ];

    $out = stream_get_contents ( $pipes [1] );
    stream_get_contents ( $pipes [2] );

    fclose ( $pipes [1] );
    fclose ( $pipes [2] );

    return [ proc_close ( $proc ), $out ];

  }

  // pad serve on a free port, one worker: is a page there to fetch while it runs, and is
  // it gone once the process is stopped. The server replaces the pad process (pcntl), so
  // stopping the process stops the server.

  function cliCheckServe () {

    $php  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
    $sock = @stream_socket_server ( 'tcp://127.0.0.1:0' );
    $port = $sock ? (int) substr ( strrchr ( stream_socket_get_name ( $sock, FALSE ), ':' ), 1 ) : 18765;

    if ( $sock )
      fclose ( $sock );

    $proc = proc_open ( [ $php, dirname ( APPS ) . '/apps/cli/pad', 'serve', (string) $port, '127.0.0.1', '--workers=1' ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'file', '/dev/null', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $pipes, NULL, getenv () );

    $body = '';

    for ( $try = 0; $try < 60 and $body === ''; $try++ ) {
      usleep ( 50000 );
      $body = (string) @file_get_contents ( "http://127.0.0.1:$port/regression/cli/?sample&padInclude&name=Bob" );
    }

    proc_terminate ( $proc );
    proc_close ( $proc );

    return trim ( $body );

  }

?>
