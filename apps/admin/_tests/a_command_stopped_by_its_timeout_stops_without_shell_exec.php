<?php

  // A host that disables shell_exec and exec - and leaves proc_open, which the console
  // needs: the search for a timed-out command's children ended the page with an Error, and
  // the command was never stopped. Tried in a PHP of its own with those functions disabled.

  $hardCode = "define ( 'DATA', " . var_export ( DATA, TRUE ) . " ); define ( 'APPS', " . var_export ( APPS, TRUE ) . " ); include " . var_export ( APP . '_lib/apps.php', TRUE ) . "; include " . var_export ( APP . '_lib/tools.php', TRUE )
            . "; \$run = adminProcess ( [ PHP_BINARY, '-r', 'sleep ( 5 );' ], 1 ); echo str_contains ( \$run [1], 'stopped after 1 seconds' ) ? 'stopped' : 'not stopped';";

  $hardProc = proc_open ( [ PHP_BINARY, '-d', 'disable_functions=shell_exec,exec', '-r', $hardCode ], [ 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ], $hardPipes );
  $hardSaid = trim ( stream_get_contents ( $hardPipes [1] ) ) ?: 'failed: ' . strtok ( stream_get_contents ( $hardPipes [2] ), "\n" );

  fclose ( $hardPipes [1] );
  fclose ( $hardPipes [2] );
  proc_close ( $hardProc );

?>
