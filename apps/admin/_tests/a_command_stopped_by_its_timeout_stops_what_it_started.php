<?php

  // A command that starts a process of its own - as pad lint starts its renders - stopped by
  // its timeout: the command was stopped, what it started went on running.

  $treeFile = DATA . 'admin-tree-test-' . getmypid () . '.pid';
  $treeCode = '$p = proc_open ( [ PHP_BINARY, "-r", "sleep ( 30 );" ], [], $x ); file_put_contents ( '
            . var_export ( $treeFile, TRUE ) . ', proc_get_status ( $p ) ["pid"] ); sleep ( 30 );';

  $treeRun  = adminProcess ( [ PHP_BINARY, '-r', $treeCode ], 1 );
  $treePid  = (int) @file_get_contents ( $treeFile );

  usleep ( 200000 );

  $treeLeft = ( $treePid and posix_kill ( $treePid, 0 ) ) ? 'still running' : 'stopped';

  if ( $treePid )
    @posix_kill ( $treePid, 9 );

  @unlink ( $treeFile );

  $treeSaid = str_contains ( $treeRun [1], 'stopped after 1 seconds' ) ? 'stopped after its timeout' : 'not stopped';

?>
