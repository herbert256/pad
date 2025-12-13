<?php


  /**
   * Gets a string representation of current level and occurrence.
   *
   * Returns the level index, optionally suffixed with occurrence number
   * if not default (0 or 99999).
   *
   * @return string Level string, e.g., "3" or "3/2" for occurrence 2.
   *
   * @global int   $pad      Current level index.
   * @global array $padOccur Array of occurrence counts by level.
   */
  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
      $return .= "/$occur";

    return $return;

  }


  /**
   * Safely reads a file's contents if it exists.
   *
   * @param string $file The file path to read.
   *
   * @return string The file contents, or empty string if file doesn't exist.
   */
  function padInfoGet ( $file ) {

    if ( ! file_exists ($file) )
      return '';

    return padFileGet ($file);

  }


  /**
   * Recursively deletes a directory and all its contents.
   *
   * @param string $dir The directory path to delete.
   *
   * @return void
   */
  function padInfoDelete ( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;

    $loop = opendir ( $dir );

    while ( ( $file = readdir ( $loop ) ) !== FALSE )

      if ( $file <> '.' and $file <> '..' )
        if ( is_dir ( "$dir/$file" ) )
          padInfoDelete ( "$dir/$file" );
        else
          unlink ( "$dir/$file" ) ;

    closedir ( $loop );

    rmdir ( $dir );

  }


?>