<?php

  // pad/pad.php fails the process when it cannot start - a variable its caller must set is
  // missing, or the application directory is not there - where its die () ended the
  // process with status 0: a command line, a CI step or a script running pad.php saw
  // success. The message still says why, on the error stream.

  function bootstrapRun ( $code ) {

    $php  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
    $proc = proc_open ( [ $php, '-r', $code . ' include ' . var_export ( PAD . 'pad.php', TRUE ) . ';' ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ], $pipes );

    if ( ! is_resource ( $proc ) )
      return 'not run';

    $out = stream_get_contents ( $pipes [1] ) . stream_get_contents ( $pipes [2] );

    fclose ( $pipes [1] );
    fclose ( $pipes [2] );

    return proc_close ( $proc ) . ' ' . trim ( preg_replace ( '#/\S*#', 'PATH', $out ) );

  }

  $bootstrapResult = implode ( ' | ', [
    bootstrapRun ( '' ),
    bootstrapRun ( '$padApps = "/no/such/apps"; $padApp = "nothing"; $padData = sys_get_temp_dir ();' )
  ] );

?>
