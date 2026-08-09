<?php

  // All file access by the engine and by applications goes through here, so that reads and
  // writes stay inside the tree and get logged.
  //
  // padFileGet   reads a file, returning $default rather than failing. A relative path is
  //              taken as relative to PAD, and only PAD, APPS and DATA are reachable;
  //              php://input is the one special case, for a raw request body
  // padFilePut   writes (or appends) under DATA only - a relative path is forced there -
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

    if ( ! str_starts_with($file, PAD) and 
         ! str_starts_with($file, APPS) and 
         ! str_starts_with($file, DATA) )
      $file = PAD . $file;

    $check = padFileCheck ( $file );
    if ( $check )
      return $default;

    if ( ! file_exists ( $file ) )
      return $default;

    if ( $padInfo )
      include PAD . 'events/get.php';

    if ( is_dir ($file) or ! is_readable ( $file ) )
      return $default;
    else
      return file_get_contents ($file);

  }


  function padFilePut ( $file, $data='', $append=0 ) {

    global $padInfo, $padDirMode, $padFileMode;

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

    if ( $data !== null and $data !== '' ) {

      if ($append) $check = file_put_contents ( $file, "$data\n", LOCK_EX | FILE_APPEND );
      else         $check = file_put_contents ( $file, $data,     LOCK_EX               );

      if ( $check === FALSE )
        return padError ( "Writing to file failed: $file" );

    }

    return TRUE;

  }


  function padFileCheck ( $file ) {

    if ( ! str_starts_with ( $file, '/' )       ) return "Invalid file (not starting with /): $file";
    if ( strpos($file, '..' ) !== FALSE         ) return "Invalid file (contains '..'): $file";
    if ( strpos($file, '//' ) !== FALSE         ) return "Invalid file (contains '//'): $file";
    if ( preg_match('/[\x00-\x1F\x7F]/', $file) ) return "Invalid file (contains control chars): $file";
                                                  return '';

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

    foreach ( padFiles ( $dir ) as $file )
      if ( is_dir ( "$dir/$file" ) and ! is_link ( "$dir/$file" ) )
        padDeleteDataDir ( "$dir/$file" );
      else
        unlink ( "$dir/$file" );

     rmdir ( $dir );

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