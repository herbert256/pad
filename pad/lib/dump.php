<?php

  // Everything the engine shows or files when a request dies: the {dump} tag, the 'dump'
  // error action, and the snapshots under DATA/dumps/<app>/.
  //
  // padDump is the entry point (padDumpTry inside an error handler, ending in padExit 500).
  // It closes any open HTML, then picks an audience: padDumpRemote for anyone but this
  // machine, who sees only the request id while the report goes to disk, and for a local
  // request padDumpConsole under the console output type and padDumpLocal - the full report
  // inline - otherwise.
  //
  // The report is assembled from single-topic collectors, each printing one block:
  // padDumpInfo (the error), padDumpStack / padDumpStackGo (backtraces, engine frames
  // shown apart from application ones), padDumpLevel / padDumpGetLevel (every level of the
  // tag stack with its tag, type, parameters, content and flags), padDumpApp, padDumpXXX
  // (globals by prefix), padDumpCurl, padDumpSQL, padDumpHeaders, padDumpRequest,
  // padDumpInput, padDumpFiles, padDumpFunctions, padDumpConstants, padDumpGlobals,
  // padDumpPhpInfo. padDumpFields sorts $GLOBALS into config, info, ids, PHP superglobals,
  // level, pad and pq groups; padDumpLines prints, with padDumpClean and padDumpShort
  // cutting values down to one readable line.
  //
  // padDumpToDir writes the same blocks as separate files (padDumpToDirGo buffers each
  // collector, padDumpFile and padDumpFilePut store them). Its Catch layers, like those in
  // lib/error.php, keep a failure while dumping from replacing the real error.
  //
  // The collectors and the writing of the report are error/_lib/dump.php, read by padDump
  // and padDumpToDir when they make one; padDumpFilePut, padDumpPrivate, padDumpInputKept
  // and padDumpPrune stay here, as the track info mode and the dump pruning use them too.

  function padDump ( $error='' ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      include_once PAD . 'error/_lib/dump.php';

      padDumpTry ( $error );

    } catch (Throwable $e) {

      padErrorStop ( $error, $e );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padDumpToDir ( $info='', $dir='' ) {

    global $padDumpToDirDone, $padLog, $padPage;

    include_once PAD . 'error/_lib/dump.php';

    if ( ! $dir )
      $dir = $padPage . '/' . $padLog . '-' . uniqid();

    if ( isset ( $padDumpToDirDone ) ) {

      if ( $dir !== $padDumpToDirDone )
        padDumpToDirDone ( $info, $dir, $padDumpToDirDone );

      return $padDumpToDirDone;

    }

    $padDumpToDirDone = $dir;

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDirGo ( $info );
      padDumpPrune ();

    } catch (Throwable $e) {

      padDumpToDirCatch ( $info, $e, $dir );

    }

    restore_error_handler ();

    return $dir;

  }

  // The request body as a report may keep it, and the extension that says what it is -
  // shared with the track info mode (info/types/track/_lib.php).

  function padDumpInputKept () {

    $txt = file_get_contents ('php://input') ?: '';

    if ( $txt === '' )
      return [ '', 'txt' ];

    $ctype = $_SERVER ['CONTENT_TYPE'] ?? '';
    $json  = json_decode ( $txt, TRUE );

    // A form is read pair by pair (padRedactQuery): parse_str warns past max_input_vars, and
    // under the track mode a post of more fields than that answered 500.

    if ( str_contains ( $ctype, 'application/x-www-form-urlencoded' ) )
      return [ padRedactText ( padRedactQuery ( $txt ) ), 'txt' ];

    if ( is_array ( $json ) )
      return [ padJson ( padRedact ( $json, '', TRUE ) ), 'json' ];

    return [ strlen ( $txt ) . ' bytes of ' . ( $ctype ?: 'an unnamed type' ) . ' - not kept', 'txt' ];

  }

  // Owner only: the file 0600 and its report directory 0700, whatever $padFileMode and
  // $padDirMode give the rest of DATA.

  function padDumpFilePut ( $file, $data ) {

    global $padApp;

    padDumpPrivate ( DATA . "dumps/$padApp" );

    padFilePut ( "dumps/$padApp/$file", $data );

    $path = DATA . "dumps/$padApp/$file";

    @chmod ( $path,            0600 );
    @chmod ( dirname ( $path ), 0700 );

  }

  // The modes come after the write, and padFilePut writes a temporary file beside its target
  // first, as the umask makes it: for that moment a report was any local user's to read.
  // The directory above them is made 0700 before anything is written into it - nothing in
  // it can be reached by another user then, whatever the files' own modes are for a moment.

  function padDumpPrivate ( $dir ) {

    if ( ! is_dir ( $dir ) )
      @mkdir ( $dir, 0700, TRUE );

    @chmod ( $dir, 0700 );

  }

  // Repeatable errors filled the disk: every one a new report directory, kept forever. The
  // newest $padErrorKeep reports of this application stay, older ones are deleted.

  function padDumpPrune () {

    global $padApp, $padErrorKeep;

    $keep = (int) ( $padErrorKeep ?? 100 );
    $root = DATA . "dumps/$padApp/";

    if ( $keep < 1 or ! is_dir ( $root ) )
      return;

    // A report is a directory that holds files. Found by them, not by depth: a page in a
    // subdirectory nests its reports one level deeper per path segment. It was a directory
    // holding a stack.html - and what a concurrent prune left was none: a report deleted
    // while its request was still writing it, the rest of its files then making the
    // directory again. Never counted, it never went: 200 errors at 16 at a time with
    // $padErrorKeep at 20 left 64 directories, globals.html in some of them.

    $reports = [];

    try {

      $files = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $root, FilesystemIterator::SKIP_DOTS ) );

      foreach ( $files as $file )
        if ( ! isset ( $reports [ $file->getPath () ] ) )
          $reports [ $file->getPath () ] = (int) @filemtime ( $file->getPath () );

    } catch ( Throwable $e ) {

      return;

    }

    if ( count ( $reports ) <= $keep )
      return;

    arsort ( $reports );

    // A report another request's prune has taken first is no reason to stop.

    foreach ( array_slice ( array_keys ( $reports ), $keep ) as $old )
      try {
        padDeleteDataDir ( $old );
      } catch ( Throwable $e ) {
      }

  }

?>
