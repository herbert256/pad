<?php

  // Four processes that add twenty lines each to the history at the same time: each wrote
  // back the list it had read, and lines were lost. The history is kept in a scratch
  // directory for the test, removed at the end; readable by its owner only.

  $historyStore              = DATA . 'admin-history-test-' . getmypid () . '/';
  $GLOBALS ['adminStore']    = $historyStore;

  $historyCode = "define ( 'DATA', " . var_export ( DATA, TRUE ) . " ); \$GLOBALS ['adminStore'] = " . var_export ( $historyStore, TRUE )
               . "; include " . var_export ( APP . '_lib/auth.php', TRUE ) . "; include " . var_export ( APP . '_lib/tools.php', TRUE )
               . "; for ( \$i = 0; \$i < 20; \$i++ ) adminHistoryAdd ( 'pad lint', 0, 1 );";

  $historyKids = [];

  for ( $historyKid = 0; $historyKid < 4; $historyKid++ )
    $historyKids [] = proc_open ( [ PHP_BINARY, '-r', $historyCode ], [], $historyPipes );

  foreach ( $historyKids as $historyKid )
    proc_close ( $historyKid );

  $historyCount = count ( adminHistory () );
  $historyMode  = sprintf ( '%o', fileperms ( $historyStore . 'history.json' ) & 0777 );

  array_map ( 'unlink', glob ( $historyStore . '*' ) ?: [] );
  @rmdir ( $historyStore );

  unset ( $GLOBALS ['adminStore'] );

?>
