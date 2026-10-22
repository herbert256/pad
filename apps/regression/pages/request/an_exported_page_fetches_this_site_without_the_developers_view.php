<?php

  // A page pad export renders fetches its own site as this machine - the JSON error report
  // the manual's template_errors shows is this machine's (request/an_exported_page_fetches_this_site_as_this_machine) -
  // but not as this machine's screen: the page it fetches leaves out the toolbar, live
  // reload, the output check and its {debug} boxes, as the exported page itself does. The
  // fetch went out as an ordinary local one, and a page {curl 'SELF://...'} brought into the
  // copy came with its {debug} box, and with the bar, its variables and SQL, where its PHP
  // switched the toolbar on.

  $exfPhp  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
  $exfProc = proc_open ( [ $exfPhp, dirname ( APPS ) . '/apps/cli/pad', 'render', 'regression/pages', 'request/fetchexported' ],
                         [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                         $exfPipes, NULL, array_merge ( getenv (), [ 'PAD_EXPORT' => '1', 'PAD_HOST' => $padHost ] ) );
  $exfCopy = is_resource ( $exfProc ) ? stream_get_contents ( $exfPipes [1] ) : '';

  if ( is_resource ( $exfProc ) ) {
    fclose ( $exfPipes [1] );
    proc_close ( $exfProc );
  }

  echo 'export: ' . ( preg_match ( '/brought ([a-z ,-]+)/', $exfCopy, $exfMatch ) ? $exfMatch [1] : 'no page' );

?>
