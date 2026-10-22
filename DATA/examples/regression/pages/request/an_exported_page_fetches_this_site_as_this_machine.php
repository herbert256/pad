<?php

  // pad export renders each page on the command line, this machine: what only this machine
  // sees stays out of the copy (request/an_exported_page_carries_nothing_only_this_machine_sees),
  // but a fetch the page makes of its own site while it is exported is still this machine's.
  // It was sent as a visitor's, and a page that fetches what only this machine gets - the
  // manual's template_errors, which shows the JSON error report - failed the export.

  $efPhp  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
  $efProc = proc_open ( [ $efPhp, dirname ( APPS ) . '/apps/cli/pad', 'render', 'regression/pages', 'request/fetching' ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $efPipes, NULL, array_merge ( getenv (), [ 'PAD_EXPORT' => '1', 'PAD_HOST' => $padHost ] ) );
  $efCopy = is_resource ( $efProc ) ? stream_get_contents ( $efPipes [1] ) : '';

  if ( is_resource ( $efProc ) ) {
    fclose ( $efPipes [1] );
    proc_close ( $efProc );
  }

  echo 'export: ' . ( preg_match ( '/answered for ([a-z ]+)/', $efCopy, $efMatch ) ? $efMatch [1] : 'no page' );

?>
