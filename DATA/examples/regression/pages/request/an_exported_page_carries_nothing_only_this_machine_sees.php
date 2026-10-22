<?php

  // pad export renders each page on the command line, which counts as this machine, for a
  // static copy any visitor reads: it gets what a visitor gets. A page whose own PHP switches
  // the toolbar, live reload or the output check on, or that shows a value with {debug},
  // carries them to this machine's browser and none of them into the export - which held
  // the bar with the page's variables, a poller of a server the copy does not have, the
  // check's panel and the {debug} box.

  $exMarks = [ 'toolbar' => 'id="padToolbar"', 'reload' => 'id="padReload"', 'check' => 'class="pad-check"', 'debug' => 'class="pad-debug"' ];

  $exLocal = (string) ( padCurl ( $padHost . 'regression/pages/?request/exported' ) ['data'] ?? '' );

  $exPhp  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
  $exProc = proc_open ( [ $exPhp, dirname ( APPS ) . '/apps/cli/pad', 'render', 'regression/pages', 'request/exported' ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $exPipes, NULL, array_merge ( getenv (), [ 'PAD_EXPORT' => '1' ] ) );
  $exCopy = is_resource ( $exProc ) ? stream_get_contents ( $exPipes [1] ) : '';

  if ( is_resource ( $exProc ) ) {
    fclose ( $exPipes [1] );
    proc_close ( $exProc );
  }

  foreach ( [ 'this machine' => $exLocal, 'export' => $exCopy ] as $exLabel => $exAnswer ) {

    $exFound = [];

    foreach ( $exMarks as $exName => $exMark )
      if ( str_contains ( $exAnswer, $exMark ) )
        $exFound [] = $exName;

    echo "$exLabel: " . ( $exFound ? implode ( ', ', $exFound ) : 'none' )
       . ( str_contains ( $exAnswer, 'an exported page' ) ? '' : ' - the page is missing' ) . "\n";

  }

?>
