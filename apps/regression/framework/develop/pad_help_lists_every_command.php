<?php

  // pad help lists every command the pad command takes: one file each in _commands/ - lib.php
  // is what they share - and sample, which the script itself handles. sample was missing.

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

  [ $code, $out, $err ] = $cliRun ( [ 'help' ] );

  $words = [ 'sample' ];

  foreach ( glob ( dirname ( APPS ) . '/apps/cli/_commands/*.php' ) as $one )
    if ( basename ( $one, '.php' ) != 'lib' )
      $words [] = basename ( $one, '.php' );

  $missing = array_filter ( $words, fn ( $word ) => ! preg_match ( "/^  pad $word\\b/m", $out ) );

  $answer = "exit $code, missing: " . ( $missing ? implode ( ' ', $missing ) : 'none' );

  unset ( $cliRun );

?>
