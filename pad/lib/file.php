<?php


  /**
   * Reads a file's contents safely.
   *
   * Handles path resolution (relative to PAD, APP, or DAT), validates
   * the file path for security, and returns file contents.
   *
   * @param string $file    The file path to read.
   * @param string $default Default value if file not readable.
   *
   * @return string File contents, default value, or FALSE on error.
   */
  function padFileGet ( $file, $default='' ) {

    if ( $file == 'php://input' )
      return file_get_contents ( 'php://input' );

    if ( ! str_starts_with($file, PAD) and ! str_starts_with($file, APP) and ! str_starts_with($file, DAT) )
      $file = PAD . $file;

    $check = padFileCheck ( $file );
    if ( $check )
      return padError ( $check );

    if ( $GLOBALS ['padInfo'] )
      include PAD . 'events/get.php';

    if ( is_dir ($file) or ! is_readable ( $file ) )
      return $default;
    else
      return file_get_contents ($file);

  }


  /**
   * Writes data to a file in the DATA directory.
   *
   * Handles path normalization (always writes to DAT), validates paths,
   * creates directories as needed, and serializes arrays/objects to JSON.
   *
   * @param string     $file   The file path (relative to DAT or absolute).
   * @param string|array $data The data to write (arrays become JSON).
   * @param int        $append If truthy, append instead of overwrite.
   *
   * @return bool|string TRUE on success, FALSE on error.
   */
  function padFilePut ( $file, $data='', $append=0 ) {

    if ( ! str_starts_with ( $file, DAT ) ) {

      if ( str_starts_with ( $file, APP ) ) $file = substr ( $file, strlen ( APP ) );
      if ( str_starts_with ( $file, PAD ) ) $file = substr ( $file, strlen ( PAD ) );

      $file = DAT . $file;

    }

    $check = padFileCheck ( $file );
    if ( $check )
      return padError ( $check );

    if ( $GLOBALS ['padInfo'] )
      include PAD . 'events/put.php';

    padFilePutGo ( $file, $data, $append );

  }


  /**
   * Validates a file path for security issues.
   *
   * Checks for path traversal attacks (..), double slashes,
   * non-absolute paths, and control characters.
   *
   * @param string $file The file path to validate.
   *
   * @return string Empty string if valid, error message if invalid.
   */
  function padFileCheck ( $file ) {

    if ( ! str_starts_with ( $file, '/' )       ) return "Invalid file (not starting with /): $file";
    if ( strpos($file, '..' ) !== FALSE         ) return "Invalid file (contains '..'): $file";
    if ( strpos($file, '//' ) !== FALSE         ) return "Invalid file (contains '//'): $file";
    if ( preg_match('/[\x00-\x1F\x7F]/', $file) ) return "Invalid file (contains control chars): $file";
                                                  return '';

  }


  /**
   * Internal file write implementation.
   *
   * Creates parent directories if needed, handles file permissions,
   * and writes data with locking.
   *
   * @param string $file   The absolute file path.
   * @param mixed  $data   The data to write.
   * @param int    $append Whether to append instead of overwrite.
   *
   * @return bool|string TRUE on success, FALSE on error.
   *
   * @global int $padDirMode  Directory creation permission mode.
   * @global int $padFileMode File creation permission mode.
   */
  function padFilePutGo ( $file, $data, $append ) {

    $dir = substr ( $file, 0, strrpos ( $file, '/' ) );

    if ( ! is_writeable ( $dir ) ) {

      if ( file_exists ( $dir)  )
        return padError ( "Directory can not be written: $dir" );

      if ( ! mkdir ($dir, $GLOBALS ['padDirMode'], true ) )
        return padError ( "Error creating directory: $dir" );

    }

    if ( ! is_writeable ( $file ) ) {

      if ( file_exists ( $file ) )
        return padError ( "File can not be written: $file" );

      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);

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


?>