<?php

  // pad sample <app> <page> holds the application to the names every other command takes:
  // ../ in it wrote _samples/ outside apps/, and an application that is not there ended on
  // the engine's die - with exit status 0, the status of a capture that worked.

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

  [ $code, $out, $err ] = $cliRun ( [ 'sample', '../DATA/no-such-app', 'index' ] );

  $answer = "exit $code: $err";

  unset ( $cliRun );

?>
