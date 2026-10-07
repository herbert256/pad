<?php

  // pad test <app> <name> with a name that matches no test of the application is a mistake,
  // not a pass: it ran nothing and said "0 tests, 0 failed" with exit status 0, so a typo
  // in a CI line turned the tests off without a word.

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

  [ $code, $out, $err ] = $cliRun ( [ 'test', 'regression/site', 'abuot' ] );

  $answer = "exit $code: $err";

  unset ( $cliRun );

?>
