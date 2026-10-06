<?php

  // The step debugger (_lib/debug.php): whether it can run here, starting and stopping the
  // debugger process, and the editor's commands passed on to it - the state (a long poll),
  // the breakpoints, stepping, and questions about the paused request.

  global $editDebug;

  if ( ! $editDebug )
    editFail ( 'the debugger is switched off: $editDebug in _config/config.php' );

  $info = editDebugInfo ();
  $dir  = editDebugDir ();
  $op   = editArg ( $body, 'op', 'status' );

  if ( $op == 'status' )
    return $info + [ 'running' => editDebugRunning ( $dir ) ];

  if ( $op == 'start' ) {
    if ( ! $info ['loaded'] )
      editFail ( 'this PHP has no Xdebug - see the editor\'s README for installing it' );
    if ( ! $info ['debug'] )
      editFail ( "Xdebug runs without its step debugger here: xdebug.mode is '" . $info ['mode'] . "', and needs debug in it" );
    editDebugStart ( $dir, $info ['port'] );
    return $info + [ 'running' => TRUE ];
  }

  if ( $op == 'stop' ) {
    editDebugStop ( $dir );
    return $info + [ 'running' => FALSE ];
  }

  $commands = [ 'state', 'breakpoints', 'run', 'step_over', 'step_into', 'step_out', 'stop_request', 'detach',
                'eval', 'property', 'frame', 'source' ];

  if ( ! in_array ( $op, $commands, TRUE ) )
    editFail ( "the debugger has no command '" . padMakeSafe ( $op, 20 ) . "'" );

  $ask = [ 'cmd' => $op == 'stop_request' ? 'stop' : $op ];

  foreach ( [ 'since', 'depth', 'context', 'page' ] as $key )
    if ( isset ( $body [$key] ) )
      $ask [$key] = (int) $body [$key];

  foreach ( [ 'expression', 'name', 'file' ] as $key )
    if ( isset ( $body [$key] ) )
      $ask [$key] = (string) $body [$key];

  if ( $op == 'breakpoints' ) {
    $ask ['exceptions'] = ! empty ( $body ['exceptions'] );
    $ask ['first']      = ! empty ( $body ['first'] );
    $ask ['list']       = [];
    foreach ( (array) ( $body ['list'] ?? [] ) as $one )
      if ( is_array ( $one ) and is_string ( $one ['file'] ?? NULL ) and (int) ( $one ['line'] ?? 0 ) > 0 )
        $ask ['list'] [] = [ 'file' => $one ['file'], 'line' => (int) $one ['line'], 'condition' => (string) ( $one ['condition'] ?? '' ) ];
  }

  $answer = editDebugAsk ( $dir, $ask, $op == 'state' ? 12 : 8 );

  if ( empty ( $answer ['ok'] ) and empty ( $answer ['off'] ) )
    editFail ( (string) ( $answer ['error'] ?? 'the debugger failed' ) );

  return $answer;

?>
