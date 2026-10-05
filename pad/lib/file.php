<?php

  // All file access by the engine and by applications goes through here, so that reads and
  // writes stay inside the tree and get logged.
  //
  // padFileGet   reads a file, returning $default rather than failing. A relative path is
  //              taken as relative to DATA, and only PAD, APPS and DATA are reachable;
  //              php://input is the one special case, for a raw request body
  // padFilePut   writes (or appends) under DATA only - a relative path is taken there -
  //              creating the directory with $padDirMode and the file with $padFileMode if
  //              needed, encoding arrays and objects as JSON, and locking the write
  // padFileCheck the shared path guard: absolute, no .., no //, no control characters. It
  //              returns a message on rejection and '' when the path is acceptable
  //
  // padDeleteDataDir removes a directory tree, but only under DATA and never following a
  //              symlink; padFiles is scandir without . and .., padGetPath is realpath
  //              with backslashes normalised.
  //
  // With info on, reads and writes are recorded through events/get.php and events/put.php.

  function padFileGet ( $file, $default='' ) {

    global $padInfo;

    if ( $file == 'php://input' )
      return file_get_contents ( 'php://input' );

    // A relative path is a DATA path, as it is to padFilePut: it was the engine's own
    // directory here, so a put and then a get of the same name met two different files.

    if ( ! str_starts_with($file, PAD) and 
         ! str_starts_with($file, APPS) and 
         ! str_starts_with($file, DATA) )
      $file = DATA . $file;

    $check = padFileCheck ( $file );
    if ( $check )
      return $default;

    if ( ! file_exists ( $file ) )
      return $default;

    if ( $padInfo )
      include PAD . 'events/get.php';

    if ( $GLOBALS ['padCoverageRun'] ?? '' )
      padCoverageFile ( $file );

    if ( is_dir ($file) or ! is_readable ( $file ) )
      return $default;

    // A template file is noted for the source map, which matches a level's text against
    // the templates read when an error has to say where it stands (lib/source.php).

    if ( str_ends_with ( $file, '.pad' ) or str_ends_with ( $file, '.html' ) )
      padSrcRead ( $file );

    return file_get_contents ($file);

  }


  function padFilePut ( $file, $data='', $append=0 ) {

    global $padInfo, $padDirMode, $padFileMode;

    // A replayed request never writes: what the application's own code asks to write is
    // answered as written and left alone; the engine's own logs and reports still go out
    // (lib/replay.php).

    if ( padReplaying () and padReplayAppWrite () )
      return TRUE;

    if ( ! str_starts_with ( $file, DATA ) )
      $file = DATA . $file;

    $check = padFileCheck ( $file );
    if ( $check )
      return padError ( $check );

    if ( $padInfo )
      include PAD . 'events/put.php';

    // No pre-flight refusals: within one long request - a build crawls the same stores
    // hundreds of times - PHP's stat cache answers is_writeable() from before the file
    // existed, and a perfectly writable file was refused as unwritable. The directory is
    // made when it is missing, a new file gets its mode, and whether the write works is
    // for the write itself to say.

    clearstatcache ( TRUE, $file );

    $dir = substr ( $file, 0, strrpos ( $file, '/' ) );

    if ( ! is_dir ( $dir ) )
      if ( ! @mkdir ( $dir, $padDirMode, true ) and ! is_dir ( $dir ) )
        return padError ( "Error creating directory: $dir" );

    if ( ! file_exists ( $file ) ) {
      @touch ( $file );
      @chmod ( $file, $padFileMode );
    }

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);

    // NULL writes nothing: the file is made when it is missing and otherwise left as it is.
    // An empty string is content like any other - overwriting with it empties the file,
    // where it used to leave the old contents in place and still answer TRUE - except when
    // appending, where it adds nothing.

    if ( $data === null or ( $append and $data === '' ) )
      return TRUE;

    // A whole write goes to a file of its own beside the target and is renamed over it.
    // Within a directory rename is atomic, so a reader - the file page cache serving a body
    // above all - sees the old contents or the new, never half a write; the readers take no
    // lock, so the LOCK_EX alone did not keep them out. The target keeps its mode.

    if ( $append )

      $check = file_put_contents ( $file, "$data\n", LOCK_EX | FILE_APPEND );

    else {

      $mode  = file_exists ( $file ) ? ( fileperms ( $file ) & 0777 ) : $padFileMode;
      $temp  = $dir . '/.' . basename ( $file ) . '.' . padRandomString ( 8 ) . '.tmp';
      $check = file_put_contents ( $temp, $data );

      if ( $check !== FALSE ) {
        @chmod ( $temp, $mode );
        if ( ! @rename ( $temp, $file ) ) {
          @unlink ( $temp );
          $check = FALSE;
        }
      }

    }

    if ( $check === FALSE )
      return padError ( "Writing to file failed: $file" );

    return TRUE;

  }


  function padFileCheck ( $file ) {

    if ( ! str_starts_with ( $file, '/' )       ) return "Invalid file (not starting with /): $file";
    if ( strpos($file, '..' ) !== FALSE         ) return "Invalid file (contains '..'): $file";
    if ( strpos($file, '//' ) !== FALSE         ) return "Invalid file (contains '//'): $file";
    if ( preg_match('/[\x00-\x1F\x7F]/', $file) ) return "Invalid file (contains control chars): $file";
                                                  return '';

  } 


  // A directory a template may list - {dir} and {files} - lies inside the applications,
  // the engine or DATA once every symlink and .. is resolved. They used the path as given,
  // and {dir '/etc'} listed whatever the PHP user could read.

  function padDirContained ( $dir ) {

    $real = padGetPath ( $dir );

    if ( $real === FALSE or ! is_dir ( $real ) )
      return FALSE;

    $real = rtrim ( $real, '/' ) . '/';

    foreach ( [ APPS, PAD, DATA ] as $root ) {

      $root = padGetPath ( $root );

      if ( $root !== FALSE and str_starts_with ( $real, rtrim ( $root, '/' ) . '/' ) )
        return TRUE;

    }

    return FALSE;

  }

  function padDeleteDataDir ( $dir ) {

    $dir = padGetPath ( $dir );

    if ( $dir === FALSE )
      return;

    if ( ! str_ends_with ( $dir, '/' ) )
      $dir .= '/';

    if ( ! file_exists     ( $dir           ) ) return;
    if ( ! is_dir          ( $dir           ) ) return;
    if ( ! str_starts_with ( $dir, DATA      ) ) return;

    // Finder drops a fresh .DS_Store into a directory it has open the moment the contents
    // change - and the write arrives a few milliseconds AFTER the deletions, asynchronously,
    // so re-scanning straight away still finds the directory empty and the rmdir loses the
    // race. The attempt is made quietly, what it leaves behind is swept again with a breath
    // in between for the event to land, and only the last attempt is allowed to shout.

    for ( $pass = 0; $pass < 5; $pass++ ) {

      foreach ( padFiles ( $dir ) as $file )
        if ( is_dir ( "$dir/$file" ) and ! is_link ( "$dir/$file" ) )
          padDeleteDataDir ( "$dir/$file" );
        else
          unlink ( "$dir/$file" );

      if ( @rmdir ( $dir ) )
        return;

      usleep ( 20000 );

    }

    // What survives five passes is Finder still re-dropping its .DS_Store: take the
    // droppings out quietly and tolerate the empty shell - a directory left standing
    // empty harms nothing, and a test run must not die for it. Anything else that
    // remains is real, and the loud rmdir below says so.

    foreach ( padFiles ( $dir ) as $file )
      if ( str_starts_with ( $file, '.' ) )
        @unlink ( "$dir/$file" );

    if ( padFiles ( $dir ) )
      rmdir ( $dir );
    else
      @rmdir ( $dir );

  }


  function padFiles ( $dir ) {

    return array_diff ( scandir ( $dir ), [ '.', '..' ] );

  }


  function padGetPath ( $file ) {

    $file = realpath ( $file );

    if ( $file === FALSE )
      return FALSE;

    return str_replace ('\\',  '/', $file );

  }

?>