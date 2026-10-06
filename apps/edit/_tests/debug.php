<?php

  // A whole debugging session, as the editor drives it, against a PHP script that Xdebug
  // runs on the command line: the debugger process started in a scratch directory on a
  // free port, a breakpoint, the stop there with its stack and variables, an expression,
  // a step, and the script let run to its end.

  $info = editDebugInfo ();
  $out  = [];

  if ( ! $info ['loaded'] or ! $info ['debug'] ) {

    $out [] = 'Xdebug with its step debugger is needed for this test';

  } else {

    $dir    = editDebugDir ( sys_get_temp_dir () . '/pad-edit-dbgtest-' . getmypid () . '/' );
    $probe  = stream_socket_server ( 'tcp://127.0.0.1:0' );
    $port   = (int) substr ( strrchr ( stream_socket_get_name ( $probe, FALSE ), ':' ), 1 );
    fclose ( $probe );

    $script = $dir . 'target.php';
    file_put_contents ( $script, "<?php\nfunction total ( \$items ) {\n  \$sum = 0;\n  foreach ( \$items as \$item )\n    \$sum += \$item;\n  return \$sum;\n}\n\$t = total ( [ 3, 4 ] );\necho \"total \$t\";\n" );

    editDebugStart ( $dir, $port );
    $out [] = 'debugger running: ' . ( editDebugRunning ( $dir ) ? 'yes' : 'no' );

    editDebugAsk ( $dir, [ 'cmd' => 'breakpoints', 'list' => [ [ 'file' => realpath ( $script ), 'line' => 6 ] ] ] );

    $proc = proc_open ( [ PHP_BINARY, '-d', 'xdebug.mode=debug', '-d', 'xdebug.start_with_request=yes', '-d', "xdebug.client_port=$port",
                          '-d', 'xdebug.client_host=127.0.0.1', $script ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ], $pipes );

    $state = [];
    for ( $try = 0; $try < 50 and ( $state ['status'] ?? '' ) !== 'break'; $try++ ) {
      $state = editDebugAsk ( $dir, [ 'cmd' => 'state', 'since' => $state ['seq'] ?? 0 ] );
      if ( ( $state ['status'] ?? '' ) !== 'break' )
        usleep ( 100000 );
    }

    $out [] = 'paused at: ' . basename ( $state ['location'] ['file'] ?? '?' ) . ':' . ( $state ['location'] ['line'] ?? '?' );
    $out [] = 'stack: ' . implode ( ' < ', array_map ( fn ( $f ) => $f ['where'] . ':' . $f ['line'], $state ['stack'] ?? [] ) );
    $out [] = 'locals: ' . implode ( ', ', array_map ( fn ( $p ) => $p ['name'] . '=' . ( $p ['children'] ? $p ['type'] . '(' . $p ['numchildren'] . ')' : $p ['value'] ), $state ['locals'] ?? [] ) );

    $eval   = editDebugAsk ( $dir, [ 'cmd' => 'eval', 'expression' => '$sum * 10' ] );
    $out [] = 'eval $sum * 10: ' . ( $eval ['property'] ['value'] ?? $eval ['error'] ?? '?' );

    $bad    = editDebugAsk ( $dir, [ 'cmd' => 'eval', 'expression' => '$sum +' ] );
    $out [] = 'a broken expression: ' . ( $bad ['ok'] ? 'answered' : 'refused' );

    $seq = $state ['seq'];
    editDebugAsk ( $dir, [ 'cmd' => 'step_out' ] );
    for ( $try = 0; $try < 50; $try++ ) {
      $state = editDebugAsk ( $dir, [ 'cmd' => 'state', 'since' => $seq ] );
      $seq   = $state ['seq'];
      if ( ( $state ['status'] ?? '' ) === 'break' and count ( $state ['stack'] ?? [] ) === 1 )
        break;
    }

    $out [] = 'stepped out to: ' . ( $state ['stack'] [0] ['where'] ?? '?' ) . ':' . ( $state ['location'] ['line'] ?? '?' );

    editDebugAsk ( $dir, [ 'cmd' => 'run' ] );

    $output = stream_get_contents ( $pipes [1] );
    fclose ( $pipes [1] );
    proc_close ( $proc );

    for ( $try = 0; $try < 30; $try++ ) {
      $state = editDebugAsk ( $dir, [ 'cmd' => 'state', 'since' => 0 ] );
      if ( ( $state ['status'] ?? '' ) === 'listening' )
        break;
      usleep ( 100000 );
    }

    $out [] = 'the script printed: ' . $output . ', the debugger: ' . ( $state ['status'] ?? '?' );

    editDebugStop ( $dir );
    $out [] = 'debugger stopped: ' . ( editDebugRunning ( $dir ) ? 'no' : 'yes' );

    editRemoveTree ( rtrim ( dirname ( $dir ), '/' ) );

  }

?>
