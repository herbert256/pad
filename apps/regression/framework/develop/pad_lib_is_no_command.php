<?php

  // lib.php in _commands/ is what the commands share, not a command: pad lib included it a
  // second time and ended on PHP's "Cannot redeclare function cliHome()", exit 255. It is
  // the cli application's page lib now, which is not there - exit 1, as for any word.

  $cliRun = function ( $args ) {
    $php  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
    $proc = proc_open ( array_merge ( [ $php, dirname ( APPS ) . '/apps/cli/pad' ], $args ),
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
                        $pipes, NULL, getenv () );
    $out = stream_get_contents ( $pipes [1] );
    $err = stream_get_contents ( $pipes [2] );
    fclose ( $pipes [1] );
    fclose ( $pipes [2] );
    return [ proc_close ( $proc ), trim ( $out ), trim ( $err ) ];
  };

  [ $code, $out, $err ] = $cliRun ( [ 'lib' ] );

  $answer = "exit $code: " . ( str_contains ( $out . $err, 'Cannot redeclare' ) ? 'redeclared' : $out );

  unset ( $cliRun );

?>
